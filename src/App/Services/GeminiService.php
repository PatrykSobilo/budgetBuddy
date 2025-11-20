<?php

declare(strict_types=1);

namespace App\Services;

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
use Framework\Database;

class GeminiService
{
    private Client $client;

    public function __construct(
        private Database $db,
        string $apiKey
    ) {
        // Use v1beta for newer Gemini 2.0+ models
        $this->client = (new Client($apiKey))->withV1BetaVersion();
    }

    /**
     * Generate financial insights for user based on their transactions
     */
    public function generateInsights(int $userId): array
    {
        // Check if we have valid cached insights
        $cachedInsights = $this->getCachedInsights($userId);
        if (!empty($cachedInsights)) {
            return $cachedInsights;
        }

        // Get user's transaction data
        $transactionData = $this->getUserTransactionSummary($userId);
        
        // Check if user has any transactions
        $hasTransactions = !empty($transactionData['expenses']) || !empty($transactionData['incomes']);
        
        if (!$hasTransactions) {
            // Return default insights for new users
            return [
                'spending_insights' => [
                    'points' => [
                        'Welcome! Start by adding your first income and expenses.',
                        'Track all transactions to get personalized financial insights.',
                        'Set category limits to monitor your spending habits.'
                    ],
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                'alerts' => [
                    'points' => ['No transactions yet. Add some to get started!'],
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                'tips' => [
                    'points' => [
                        'Begin tracking your expenses to understand spending patterns.',
                        'Add income sources to see your financial overview.',
                        'Regular tracking helps identify savings opportunities.'
                    ],
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ];
        }
        
        // Generate insights using AI
        $insights = [
            'spending_insights' => $this->generateSpendingInsights($userId, $transactionData),
            'alerts' => $this->generateAlerts($userId, $transactionData),
            'tips' => $this->generateSavingTips($userId, $transactionData)
        ];

        // Cache insights for 24 hours
        $this->cacheInsights($userId, $insights);

        return $insights;
    }

    /**
     * Get cached insights if still valid
     */
    private function getCachedInsights(int $userId): array
    {
        $now = date('Y-m-d H:i:s');
        
        $insights = $this->db->query(
            "SELECT insight_type, content 
             FROM ai_insights 
             WHERE user_id = :user_id 
             AND valid_until > :now
             ORDER BY insight_type ASC",
            [
                'user_id' => $userId,
                'now' => $now
            ]
        )->findAll();

        if (empty($insights)) {
            return [];
        }

        $result = [];
        foreach ($insights as $insight) {
            $result[$insight['insight_type']] = json_decode($insight['content'], true);
        }

        // Only return if we have all three types
        if (count($result) === 3) {
            return $result;
        }

        return [];
    }

    /**
     * Cache insights for 24 hours
     */
    private function cacheInsights(int $userId, array $insights): void
    {
        $validUntil = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Delete old insights
        $this->db->query(
            "DELETE FROM ai_insights WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        // Insert new insights
        foreach ($insights as $type => $content) {
            $this->db->query(
                "INSERT INTO ai_insights (user_id, insight_type, content, valid_until) 
                 VALUES (:user_id, :type, :content, :valid_until)",
                [
                    'user_id' => $userId,
                    'type' => $type,
                    'content' => json_encode($content),
                    'valid_until' => $validUntil
                ]
            );
        }
    }

    /**
     * Get user transaction summary for AI analysis
     */
    private function getUserTransactionSummary(int $userId): array
    {
        // Current month expenses by category (summary)
        $expenses = $this->db->query(
            "SELECT 
                ec.name as category,
                ec.category_limit as limit_amount,
                SUM(e.amount) as total_spent,
                COUNT(*) as transaction_count,
                AVG(e.amount) as avg_transaction
             FROM expenses e
             JOIN expenses_category_assigned_to_users ec ON e.expense_category_assigned_to_user_id = ec.id
             WHERE e.user_id = :user_id 
             AND YEAR(e.date_of_expense) = YEAR(NOW())
             AND MONTH(e.date_of_expense) = MONTH(NOW())
             GROUP BY ec.id, ec.name, ec.category_limit
             ORDER BY total_spent DESC",
            ['user_id' => $userId]
        )->findAll();

        // Current month incomes (summary)
        $incomes = $this->db->query(
            "SELECT 
                ic.name as category,
                SUM(i.amount) as total_income,
                COUNT(*) as transaction_count
             FROM incomes i
             JOIN incomes_category_assigned_to_users ic ON i.income_category_assigned_to_user_id = ic.id
             WHERE i.user_id = :user_id 
             AND YEAR(i.date_of_income) = YEAR(NOW())
             AND MONTH(i.date_of_income) = MONTH(NOW())
             GROUP BY ic.id, ic.name",
            ['user_id' => $userId]
        )->findAll();

        // ALL expense transactions from current month (no limit)
        $expenseTransactions = $this->db->query(
            "SELECT 
                e.amount,
                e.date_of_expense as date,
                e.expense_comment as comment,
                ec.name as category
             FROM expenses e
             JOIN expenses_category_assigned_to_users ec ON e.expense_category_assigned_to_user_id = ec.id
             WHERE e.user_id = :user_id 
             AND YEAR(e.date_of_expense) = YEAR(NOW())
             AND MONTH(e.date_of_expense) = MONTH(NOW())
             ORDER BY e.date_of_expense DESC",
            ['user_id' => $userId]
        )->findAll();

        // ALL income transactions from current month (no limit)
        $incomeTransactions = $this->db->query(
            "SELECT 
                i.amount,
                i.date_of_income as date,
                i.income_comment as comment,
                ic.name as category
             FROM incomes i
             JOIN incomes_category_assigned_to_users ic ON i.income_category_assigned_to_user_id = ic.id
             WHERE i.user_id = :user_id 
             AND YEAR(i.date_of_income) = YEAR(NOW())
             AND MONTH(i.date_of_income) = MONTH(NOW())
             ORDER BY i.date_of_income DESC",
            ['user_id' => $userId]
        )->findAll();

        // Current month totals
        $totals = $this->db->query(
            "SELECT 
                COALESCE((SELECT SUM(amount) FROM incomes WHERE user_id = :user_id AND YEAR(date_of_income) = YEAR(NOW()) AND MONTH(date_of_income) = MONTH(NOW())), 0) as total_income,
                COALESCE((SELECT SUM(amount) FROM expenses WHERE user_id = :user_id AND YEAR(date_of_expense) = YEAR(NOW()) AND MONTH(date_of_expense) = MONTH(NOW())), 0) as total_expenses",
            ['user_id' => $userId]
        )->find();

        // Get historical comparison (last 3 months average)
        $historical = $this->db->query(
            "SELECT 
                AVG(total_income) as avg_income,
                AVG(total_expenses) as avg_expenses,
                AVG(transaction_count) as avg_transactions
             FROM ai_monthly_summaries
             WHERE user_id = :user_id
             AND (year < YEAR(NOW()) OR (year = YEAR(NOW()) AND month < MONTH(NOW())))
             ORDER BY year DESC, month DESC
             LIMIT 3",
            ['user_id' => $userId]
        )->find();

        // Get previous month's AI summary and recommendations
        $previousMonth = $this->db->query(
            "SELECT ai_summary, key_issues, recommendations 
             FROM ai_monthly_summaries
             WHERE user_id = :user_id
             AND is_finalized = 1
             ORDER BY year DESC, month DESC
             LIMIT 1",
            ['user_id' => $userId]
        )->find();

        // Check if we need to finalize previous month
        $this->finalizePreviousMonth($userId);

        // Update current month summary (statistics only, not finalized yet)
        $this->updateMonthlySummary($userId, $totals, count($expenseTransactions) + count($incomeTransactions), $expenses);

        return [
            'expenses' => $expenses,
            'incomes' => $incomes,
            'expense_transactions' => $expenseTransactions,
            'income_transactions' => $incomeTransactions,
            'totals' => $totals,
            'historical' => $historical ?? ['avg_income' => 0, 'avg_expenses' => 0, 'avg_transactions' => 0],
            'previous_summary' => $previousMonth ?? null
        ];
    }

    /**
     * Update monthly summary for historical comparison
     */
    private function updateMonthlySummary(int $userId, array $totals, int $transactionCount, array $expenses): void
    {
        $year = date('Y');
        $month = date('n');
        
        $topCategory = !empty($expenses) ? $expenses[0]['category'] : null;
        $topAmount = !empty($expenses) ? $expenses[0]['total_spent'] : 0;

        $this->db->query(
            "INSERT INTO ai_monthly_summaries 
                (user_id, year, month, total_income, total_expenses, transaction_count, top_expense_category, top_expense_amount)
             VALUES 
                (:user_id, :year, :month, :income, :expenses, :count, :category, :amount)
             ON DUPLICATE KEY UPDATE
                total_income = :income,
                total_expenses = :expenses,
                transaction_count = :count,
                top_expense_category = :category,
                top_expense_amount = :amount",
            [
                'user_id' => $userId,
                'year' => $year,
                'month' => $month,
                'income' => $totals['total_income'],
                'expenses' => $totals['total_expenses'],
                'count' => $transactionCount,
                'category' => $topCategory,
                'amount' => $topAmount
            ]
        );
    }

    /**
     * Finalize previous month with AI-generated summary
     */
    private function finalizePreviousMonth(int $userId): void
    {
        // Get last month's year and month
        $lastMonth = date('Y-m-d', strtotime('first day of last month'));
        $year = date('Y', strtotime($lastMonth));
        $month = date('n', strtotime($lastMonth));

        // Check if previous month exists and is not finalized
        $previousMonth = $this->db->query(
            "SELECT id, total_income, total_expenses, top_expense_category, is_finalized
             FROM ai_monthly_summaries
             WHERE user_id = :user_id
             AND year = :year
             AND month = :month",
            [
                'user_id' => $userId,
                'year' => $year,
                'month' => $month
            ]
        )->find();

        // If month exists and not finalized, generate AI summary
        if ($previousMonth && !$previousMonth['is_finalized']) {
            $this->generateMonthlySummary($userId, $year, $month);
        }
    }

    /**
     * Generate AI summary for completed month
     */
    private function generateMonthlySummary(int $userId, int $year, int $month): void
    {
        // Get all transactions from that month
        $expenseTransactions = $this->db->query(
            "SELECT 
                e.amount,
                e.date_of_expense as date,
                e.expense_comment as comment,
                ec.name as category
             FROM expenses e
             JOIN expenses_category_assigned_to_users ec ON e.expense_category_assigned_to_user_id = ec.id
             WHERE e.user_id = :user_id 
             AND YEAR(e.date_of_expense) = :year
             AND MONTH(e.date_of_expense) = :month
             ORDER BY e.date_of_expense DESC",
            ['user_id' => $userId, 'year' => $year, 'month' => $month]
        )->findAll();

        // Get category breakdown
        $expenses = $this->db->query(
            "SELECT 
                ec.name as category,
                SUM(e.amount) as total_spent,
                COUNT(*) as transaction_count
             FROM expenses e
             JOIN expenses_category_assigned_to_users ec ON e.expense_category_assigned_to_user_id = ec.id
             WHERE e.user_id = :user_id 
             AND YEAR(e.date_of_expense) = :year
             AND MONTH(e.date_of_expense) = :month
             GROUP BY ec.id, ec.name
             ORDER BY total_spent DESC",
            ['user_id' => $userId, 'year' => $year, 'month' => $month]
        )->findAll();

        // Get month totals
        $totals = $this->db->query(
            "SELECT 
                COALESCE((SELECT SUM(amount) FROM incomes WHERE user_id = :user_id AND YEAR(date_of_income) = :year AND MONTH(date_of_income) = :month), 0) as total_income,
                COALESCE((SELECT SUM(amount) FROM expenses WHERE user_id = :user_id AND YEAR(date_of_expense) = :year AND MONTH(date_of_expense) = :month), 0) as total_expenses",
            ['user_id' => $userId, 'year' => $year, 'month' => $month]
        )->find();

        // Build prompt for monthly summary
        $prompt = $this->buildMonthlySummaryPrompt($expenses, $expenseTransactions, $totals, $year, $month);

        try {
            $response = $this->client
                ->generativeModel('gemini-flash-latest')
                ->generateContent(new TextPart($prompt));

            $summaryData = $this->parseMonthlySummary($response->text());

            // Save AI summary to database
            $this->db->query(
                "UPDATE ai_monthly_summaries 
                 SET ai_summary = :summary,
                     key_issues = :issues,
                     recommendations = :recommendations,
                     is_finalized = 1
                 WHERE user_id = :user_id
                 AND year = :year
                 AND month = :month",
                [
                    'user_id' => $userId,
                    'year' => $year,
                    'month' => $month,
                    'summary' => $summaryData['summary'],
                    'issues' => $summaryData['issues'],
                    'recommendations' => $summaryData['recommendations']
                ]
            );
        } catch (\Exception $e) {
            // If AI fails, mark as finalized without summary
            $this->db->query(
                "UPDATE ai_monthly_summaries 
                 SET is_finalized = 1
                 WHERE user_id = :user_id
                 AND year = :year
                 AND month = :month",
                ['user_id' => $userId, 'year' => $year, 'month' => $month]
            );
        }
    }

    /**
     * Build prompt for monthly summary generation
     */
    private function buildMonthlySummaryPrompt(array $expenses, array $transactions, array $totals, int $year, int $month): string
    {
        $monthName = date('F', mktime(0, 0, 0, $month, 1, $year));
        $totalIncome = $totals['total_income'] ?? 0;
        $totalExpenses = $totals['total_expenses'] ?? 0;

        $expenseBreakdown = '';
        foreach ($expenses as $expense) {
            $expenseBreakdown .= "- {$expense['category']}: \${$expense['total_spent']} ({$expense['transaction_count']} transactions)\n";
        }

        $transactionList = '';
        foreach ($transactions as $tx) {
            $comment = !empty($tx['comment']) ? " - {$tx['comment']}" : '';
            $transactionList .= "- {$tx['date']}: \${$tx['amount']} ({$tx['category']}){$comment}\n";
        }

        return "You are a financial psychologist and advisor. Analyze this user's {$monthName} {$year} spending and create a comprehensive monthly summary.

Month Overview:
- Income: \${$totalIncome}
- Expenses: \${$totalExpenses}
- Balance: \$" . ($totalIncome - $totalExpenses) . "

Expense Breakdown:
{$expenseBreakdown}

All Transactions:
{$transactionList}

Provide your analysis in this EXACT format:

SUMMARY:
[Write 2-3 sentences about overall spending patterns, what went well and what didn't]

KEY_ISSUES:
- [Issue 1: e.g., 'Too much spending on FastFood - $250 in 15 transactions']
- [Issue 2: e.g., 'Impulse purchases in Computer Games category']
- [Issue 3: if applicable]

RECOMMENDATIONS:
- [Specific advice for next month, e.g., 'Try limiting FastFood to $150 next month']
- [Psychological insight, e.g., 'Notice you spend more on FastFood on weekends - plan meals ahead']
- [Actionable tip with empathy]

Be specific, empathetic, and actionable. Identify patterns that user should be aware of for next month.";
    }

    /**
     * Parse monthly summary response from AI
     */
    private function parseMonthlySummary(string $response): array
    {
        $summary = '';
        $issues = '';
        $recommendations = '';

        // Extract sections
        if (preg_match('/SUMMARY:\s*(.*?)\s*KEY_ISSUES:/s', $response, $matches)) {
            $summary = trim($matches[1]);
        }

        if (preg_match('/KEY_ISSUES:\s*(.*?)\s*RECOMMENDATIONS:/s', $response, $matches)) {
            $issues = trim($matches[1]);
        }

        if (preg_match('/RECOMMENDATIONS:\s*(.*?)$/s', $response, $matches)) {
            $recommendations = trim($matches[1]);
        }

        return [
            'summary' => $summary ?: 'No summary available',
            'issues' => $issues ?: 'No issues identified',
            'recommendations' => $recommendations ?: 'Keep tracking your expenses'
        ];
    }

    /**
     * Generate spending insights using AI
     */
    private function generateSpendingInsights(int $userId, array $data): array
    {
        $prompt = $this->buildSpendingInsightsPrompt($data);
        
        try {
            $response = $this->client
                ->generativeModel('gemini-flash-latest')
                ->generateContent(new TextPart($prompt));

            $insights = $this->parseInsightsResponse($response->text());
            
            return [
                'points' => $insights,
                'updated_at' => date('Y-m-d H:i:s')
            ];
        } catch (\Exception $e) {
            return [
                'points' => ['Unable to generate insights at this time.'],
                'updated_at' => date('Y-m-d H:i:s'),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate alerts using AI
     */
    private function generateAlerts(int $userId, array $data): array
    {
        $prompt = $this->buildAlertsPrompt($data);
        
        try {
            $response = $this->client
                ->generativeModel('gemini-flash-latest')
                ->generateContent(new TextPart($prompt));

            $alerts = $this->parseInsightsResponse($response->text());
            
            return [
                'points' => $alerts,
                'updated_at' => date('Y-m-d H:i:s')
            ];
        } catch (\Exception $e) {
            return [
                'points' => ['No alerts at this time.'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
    }

    /**
     * Generate saving tips using AI
     */
    private function generateSavingTips(int $userId, array $data): array
    {
        $prompt = $this->buildSavingTipsPrompt($data);
        
        try {
            $response = $this->client
                ->generativeModel('gemini-flash-latest')
                ->generateContent(new TextPart($prompt));

            $tips = $this->parseInsightsResponse($response->text());
            
            return [
                'points' => $tips,
                'updated_at' => date('Y-m-d H:i:s')
            ];
        } catch (\Exception $e) {
            return [
                'points' => ['Keep tracking your expenses!'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
    }

    /**
     * Build prompt for spending insights
     */
    private function buildSpendingInsightsPrompt(array $data): string
    {
        $totalIncome = $data['totals']['total_income'] ?? 0;
        $totalExpenses = $data['totals']['total_expenses'] ?? 0;
        $balance = $totalIncome - $totalExpenses;

        // Historical comparison
        $historical = $data['historical'];
        $avgIncome = $historical['avg_income'] ?? 0;
        $avgExpenses = $historical['avg_expenses'] ?? 0;
        $incomeChange = $avgIncome > 0 ? round((($totalIncome - $avgIncome) / $avgIncome) * 100, 1) : 0;
        $expenseChange = $avgExpenses > 0 ? round((($totalExpenses - $avgExpenses) / $avgExpenses) * 100, 1) : 0;

        // Previous month's advice
        $previousContext = '';
        if (!empty($data['previous_summary'])) {
            $previousContext = "\nPREVIOUS MONTH'S ADVICE:\n";
            $previousContext .= "Issues identified: " . ($data['previous_summary']['key_issues'] ?? 'None') . "\n";
            $previousContext .= "Recommendations given: " . ($data['previous_summary']['recommendations'] ?? 'None') . "\n";
            $previousContext .= "\nIMPORTANT: Check if user followed advice or if same problems persist!\n";
        }

        $expenseBreakdown = '';
        foreach ($data['expenses'] as $expense) {
            $expenseBreakdown .= "- {$expense['category']}: \${$expense['total_spent']} ({$expense['transaction_count']} transactions)\n";
        }

        // All transactions from current month
        $transactionDetails = '';
        $transactionCount = count($data['expense_transactions'] ?? []);
        foreach ($data['expense_transactions'] ?? [] as $tx) {
            $comment = !empty($tx['comment']) ? " - {$tx['comment']}" : '';
            $transactionDetails .= "- {$tx['date']}: \${$tx['amount']} ({$tx['category']}){$comment}\n";
        }

        return "You are a personal finance advisor with memory. Analyze this user's CURRENT MONTH spending and provide EXACTLY 3 concise insights (max 15 words each).

Current Month Summary:
- Income: \${$totalIncome}
- Expenses: \${$totalExpenses}
- Balance: \${$balance}
- Total Transactions: {$transactionCount}

Historical Comparison (vs 3-month average):
- Income change: {$incomeChange}%
- Expense change: {$expenseChange}%
- Previous avg income: \${$avgIncome}
- Previous avg expenses: \${$avgExpenses}
{$previousContext}

Expense Breakdown by Category:
{$expenseBreakdown}

ALL Transactions This Month:
{$transactionDetails}

Provide 3 bullet points with spending insights. Format as:
- Insight 1
- Insight 2
- Insight 3

Focus on: specific categories (e.g. 'Computer Games'), compare to previous advice (did they improve? same issues?), spending trends, patterns. If same problem persists (e.g., still too much FastFood), mention it directly!";
    }

    /**
     * Build prompt for alerts
     */
    private function buildAlertsPrompt(array $data): string
    {
        $alerts = [];
        
        foreach ($data['expenses'] as $expense) {
            if ($expense['limit_amount'] && $expense['total_spent'] > $expense['limit_amount']) {
                $alerts[] = "- {$expense['category']}: Over limit by \$" . number_format($expense['total_spent'] - $expense['limit_amount'], 2);
            }
        }

        if (empty($alerts)) {
            $totalExpenses = $data['totals']['total_expenses'] ?? 0;
            $totalIncome = $data['totals']['total_income'] ?? 0;
            
            if ($totalExpenses > $totalIncome * 0.9) {
                $alerts[] = "- High spending: Using " . round(($totalExpenses / $totalIncome) * 100) . "% of income";
            }
        }

        $alertsText = implode("\n", $alerts);

        return "You are a financial alert system. Based on these issues, provide EXACTLY 2-3 SHORT alerts (max 12 words each):

Issues detected:
{$alertsText}

Format as:
- Alert 1
- Alert 2

Be direct and urgent. Focus on immediate concerns.";
    }

    /**
     * Build prompt for saving tips
     */
    private function buildSavingTipsPrompt(array $data): string
    {
        $topCategories = array_slice($data['expenses'], 0, 5);
        $categoryList = '';
        
        foreach ($topCategories as $cat) {
            $categoryList .= "- {$cat['category']}: \${$cat['total_spent']} ({$cat['transaction_count']} transactions, avg \${$cat['avg_transaction']})\n";
        }

        // Historical context
        $historical = $data['historical'];
        $avgExpenses = $historical['avg_expenses'] ?? 0;
        $currentExpenses = $data['totals']['total_expenses'] ?? 0;

        return "You are a savings coach. Based on current month spending, provide EXACTLY 3 practical tips (max 15 words each):

Current month expenses: \${$currentExpenses}
Historical average: \${$avgExpenses}

Top spending categories this month:
{$categoryList}

Format as:
- Tip 1
- Tip 2
- Tip 3

Be specific about categories (e.g., 'Reduce Computer Games spending by 30%'), compare to historical data, actionable, and realistic.";
    }

    /**
     * Parse AI response into array of points
     */
    private function parseInsightsResponse(string $response): array
    {
        // Split by lines and filter bullet points
        $lines = explode("\n", $response);
        $points = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            // Match lines starting with -, *, or numbers
            if (preg_match('/^[-*â€¢]\s*(.+)$/', $line, $matches)) {
                $points[] = trim($matches[1]);
            } elseif (preg_match('/^\d+\.\s*(.+)$/', $line, $matches)) {
                $points[] = trim($matches[1]);
            }
        }

        // Return first 3 points, or fallback
        return array_slice($points, 0, 3) ?: ['Analysis in progress...'];
    }

    /**
     * Start or continue chat session
     */
    public function chat(int $userId, string $userMessage): array
    {
        // Save user message
        $this->saveChatMessage($userId, 'user', $userMessage);

        // Get chat history
        $history = $this->getChatHistory($userId, 10);

        // Get user context
        $transactionData = $this->getUserTransactionSummary($userId);
        $contextPrompt = $this->buildChatContextPrompt($transactionData);

        // Build conversation
        $fullPrompt = "{$contextPrompt}\n\nUser: {$userMessage}";

        try {
            $response = $this->client
                ->generativeModel('gemini-flash-latest')
                ->generateContent(new TextPart($fullPrompt));

            $aiResponse = $response->text();

            // Save AI response
            $this->saveChatMessage($userId, 'model', $aiResponse);

            return [
                'success' => true,
                'message' => $aiResponse
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Sorry, I encountered an error. Please try again.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Save chat message to database
     */
    private function saveChatMessage(int $userId, string $role, string $message): void
    {
        $this->db->query(
            "INSERT INTO ai_chat_sessions (user_id, role, message) 
             VALUES (:user_id, :role, :message)",
            [
                'user_id' => $userId,
                'role' => $role,
                'message' => $message
            ]
        );
    }

    /**
     * Get chat history
     */
    private function getChatHistory(int $userId, int $limit = 10): array
    {
        // Note: LIMIT cannot be bound as parameter in some DB systems, use direct integer
        return $this->db->query(
            "SELECT role, message, created_at 
             FROM ai_chat_sessions 
             WHERE user_id = :user_id 
             ORDER BY created_at DESC 
             LIMIT {$limit}",
            [
                'user_id' => $userId
            ]
        )->findAll();
    }

    /**
     * Build context prompt for chat
     */
    private function buildChatContextPrompt(array $data): string
    {
        $totalIncome = $data['totals']['total_income'] ?? 0;
        $totalExpenses = $data['totals']['total_expenses'] ?? 0;

        // Historical comparison
        $historical = $data['historical'];
        $avgIncome = $historical['avg_income'] ?? 0;
        $avgExpenses = $historical['avg_expenses'] ?? 0;

        // Previous month's context
        $previousContext = '';
        if (!empty($data['previous_summary'])) {
            $previousContext = "\nPrevious Month's Summary:\n" . ($data['previous_summary']['ai_summary'] ?? '') . "\n\n";
            $previousContext .= "Issues we identified:\n" . ($data['previous_summary']['key_issues'] ?? '') . "\n\n";
            $previousContext .= "Recommendations we gave:\n" . ($data['previous_summary']['recommendations'] ?? '') . "\n\n";
            $previousContext .= "IMPORTANT: Reference this history in conversations. If user asks about progress or if same issues persist, compare current behavior to previous advice.\n";
        }

        // Expense breakdown by category
        $expenseBreakdown = '';
        foreach ($data['expenses'] as $expense) {
            $expenseBreakdown .= "- {$expense['category']}: \${$expense['total_spent']} ({$expense['transaction_count']} transactions)\n";
        }

        // All transactions from current month (limit to last 30 for chat context)
        $recentTransactions = '';
        $transactions = array_slice($data['expense_transactions'] ?? [], 0, 30);
        foreach ($transactions as $tx) {
            $comment = !empty($tx['comment']) ? " - {$tx['comment']}" : '';
            $recentTransactions .= "- {$tx['date']}: \${$tx['amount']} ({$tx['category']}){$comment}\n";
        }

        return "You are a personal financial advisor and psychologist with continuity. The user has:

Current Month Summary:
- Income: \${$totalIncome}
- Expenses: \${$totalExpenses}
- Balance: \$" . ($totalIncome - $totalExpenses) . "

Historical Comparison (3-month average):
- Average Income: \${$avgIncome}
- Average Expenses: \${$avgExpenses}
{$previousContext}

Expenses by Category This Month:
{$expenseBreakdown}

Recent Transactions (last 30):
{$recentTransactions}

Be helpful, empathetic, and specific. Reference previous advice when relevant. If user asks why they spend on something (e.g., FastFood), explore psychological reasons (stress, convenience, habits). If same problems persist, gently point it out and offer deeper insights or different strategies. Compare current behavior to previous recommendations.";
    }

    /**
     * Clear chat history for user
     */
    public function clearChatHistory(int $userId): void
    {
        $this->db->query(
            "DELETE FROM ai_chat_sessions WHERE user_id = :user_id",
            ['user_id' => $userId]
        );
    }
}

