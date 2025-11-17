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
        // Last 30 days expenses by category
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
             AND e.date_of_expense >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY ec.id, ec.name, ec.category_limit
             ORDER BY total_spent DESC",
            ['user_id' => $userId]
        )->findAll();

        // Last 30 days incomes
        $incomes = $this->db->query(
            "SELECT 
                ic.name as category,
                SUM(i.amount) as total_income
             FROM incomes i
             JOIN incomes_category_assigned_to_users ic ON i.income_category_assigned_to_user_id = ic.id
             WHERE i.user_id = :user_id 
             AND i.date_of_income >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY ic.id, ic.name",
            ['user_id' => $userId]
        )->findAll();

        // Total income vs expenses
        $totals = $this->db->query(
            "SELECT 
                COALESCE((SELECT SUM(amount) FROM incomes WHERE user_id = :user_id AND date_of_income >= DATE_SUB(NOW(), INTERVAL 30 DAY)), 0) as total_income,
                COALESCE((SELECT SUM(amount) FROM expenses WHERE user_id = :user_id AND date_of_expense >= DATE_SUB(NOW(), INTERVAL 30 DAY)), 0) as total_expenses",
            ['user_id' => $userId]
        )->find();

        return [
            'expenses' => $expenses,
            'incomes' => $incomes,
            'totals' => $totals
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

        $expenseBreakdown = '';
        foreach ($data['expenses'] as $expense) {
            $expenseBreakdown .= "- {$expense['category']}: \${$expense['total_spent']} ({$expense['transaction_count']} transactions)\n";
        }

        return "You are a personal finance advisor. Analyze this user's last 30 days of spending and provide EXACTLY 3 concise insights (max 15 words each).

Financial Summary:
- Total Income: \${$totalIncome}
- Total Expenses: \${$totalExpenses}
- Balance: \${$balance}

Expense Breakdown:
{$expenseBreakdown}

Provide 3 bullet points with spending insights. Format as:
- Insight 1
- Insight 2
- Insight 3

Focus on patterns, trends, and comparisons. Be specific and actionable.";
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
        $topCategories = array_slice($data['expenses'], 0, 3);
        $categoryList = '';
        
        foreach ($topCategories as $cat) {
            $categoryList .= "- {$cat['category']}: \${$cat['total_spent']}\n";
        }

        return "You are a savings coach. Based on this spending, provide EXACTLY 3 practical tips (max 15 words each):

Top spending categories:
{$categoryList}

Format as:
- Tip 1
- Tip 2
- Tip 3

Be specific, actionable, and realistic.";
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

        return "You are a personal financial advisor. The user has:
- Income (last 30 days): \${$totalIncome}
- Expenses (last 30 days): \${$totalExpenses}
- Balance: \$" . ($totalIncome - $totalExpenses) . "

Be helpful, concise, and specific. Answer financial questions based on their data.";
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

