<?php 
$pageScripts = ['charts-dashboards.js', 'category-limits.js', 'ai-advisor.js'];
include $this->resolve("partials/_header.php"); 
?>
<?php include $this->resolve("transactions/_transactionButtons.php", ['csrfToken' => $csrfToken]); ?>

<div class="container mt-4">
    <h1 class="text-center mb-4 h2">Dashboards & AI Advisor</h1>
</div>

<section id="userParameters" name="userParameters">
    <div class="container">
        <?php 
        $action = '/dashboards';
        $method = 'POST';
        $startDateName = 'startingDate';
        $endDateName = 'endingDate';
        $submitLabel = 'Show Balance';
        $onChangeHandler = 'toggleCustomDatesDashboard';
        include $this->resolve("partials/_periodFilter.php");
        ?>
    </div>
</section>



<section id="summary" name="summary">
    <div id="generalSummary" class="generalSummary container d-flex flex-column align-items-center justify-content-center border">
        <div class="d-flex flex-column align-items-center w-100">
            <div class="table-container w-auto">
                <div class="container mt-5 text-center">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0">Balance Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mx-auto mb-0" name="balance" style="width:auto;">
                                    <thead>
                                        <tr>
                                            <th>Expenses</th>
                                            <th>Incomes</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <?php
                                            $service = $transactionService ?? null;
                                            $startDate = $startDate ?? null;
                                            $endDate = $endDate ?? null;
                                            $userId = $userId ?? null;
                                            $summary = ($service && $userId) ? $service->calculateTransactions($userId, $startDate, $endDate) : ['expenses' => 0, 'incomes' => 0, 'balance' => 0];
                                            ?>
                                            <td><?php echo number_format($summary['expenses'], 2, '.', ' '); ?></td>
                                            <td><?php echo number_format($summary['incomes'], 2, '.', ' '); ?></td>
                                            <td><?php echo number_format($summary['balance'], 2, '.', ' '); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container mt-4 mb-4 text-center">
                    <h4 class="mb-3">Expenses vs Incomes</h4>
                    <canvas id="summaryPieChart" width="300" height="300" style="display: block; margin: 0 auto;"></canvas>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            </div>
        </div>
    </div>
</section>

<!-- AI Financial Advisor Section -->
<section id="ai-advisor" class="mt-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3">🤖 AI Financial Advisor</h2>
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshInsights()">
                    🔄 Refresh Insights
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#chatModal">
                    💬 Chat with Advisor
                </button>
            </div>
        </div>

        <div class="row g-3" id="insightsContainer">
            <!-- Spending Insights Card -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">💡 Spending Insights</h5>
                    </div>
                    <div class="card-body">
                        <div id="spending-insights-content">
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-3" id="spending-insights-time">Loading...</small>
                    </div>
                </div>
            </div>

            <!-- Alerts Card -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">⚠️ Alerts</h5>
                    </div>
                    <div class="card-body">
                        <div id="alerts-content">
                            <div class="text-center py-3">
                                <div class="spinner-border text-warning" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-3" id="alerts-time">Loading...</small>
                    </div>
                </div>
            </div>

            <!-- Saving Tips Card -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">🎯 Saving Tips</h5>
                    </div>
                    <div class="card-body">
                        <div id="tips-content">
                            <div class="text-center py-3">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-3" id="tips-time">Loading...</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Summaries Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <button class="btn btn-sm btn-light" onclick="previousMonth()" id="prevMonthBtn">
                            ← Previous
                        </button>
                        <h5 class="mb-0" id="summaryMonthTitle">📊 Monthly Summary</h5>
                        <button class="btn btn-sm btn-light" onclick="nextMonth()" id="nextMonthBtn">
                            Next →
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="monthly-summary-content">
                            <div class="text-center py-5 text-muted">
                                <p>Select a month to view AI-generated summary</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 70%;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title me-auto" id="chatModalLabel">💬 Chat with AI Financial Advisor</h5>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="clearChatHistory()" title="Clear chat history">
                        <i class="bi bi-trash"></i> Clear History
                    </button>
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal" aria-label="Close">
                        ✕
                    </button>
                </div>
            </div>
            <div class="modal-body" style="height: 500px; overflow-y: auto;" id="chatMessages">
                <div class="text-center text-muted py-5">
                    <h5>Ask me anything about your finances!</h5>
                    <p>Try the quick actions below or type your own question.</p>
                </div>
            </div>
            <div class="modal-footer flex-column align-items-stretch">
                <!-- Quick Actions -->
                <div class="d-flex gap-2 mb-2 flex-wrap">
                    <button class="btn btn-sm btn-outline-primary" onclick="sendQuickQuestion('Analyze my spending this month')">
                        📊 Analyze my spending
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="sendQuickQuestion('Where can I save money?')">
                        💰 Where to save?
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="sendQuickQuestion('Am I overspending in any category?')">
                        ⚠️ Check overspending
                    </button>
                </div>
                <!-- Message Input -->
                <div class="input-group">
                    <input type="text" class="form-control" id="chatInput" placeholder="Type your question..." 
                           onkeypress="if(event.key === 'Enter') sendChatMessage()">
                    <button class="btn btn-primary" type="button" onclick="sendChatMessage()">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $this->resolve("partials/_footer.php"); ?>

<script>
  // CSRF token for AJAX requests
  window.csrfToken = <?php echo json_encode($csrfToken ?? ''); ?>;
  
  // Initialize dashboard chart when DOM is ready
  document.addEventListener('DOMContentLoaded', function() {
    initializeSummaryChart(
      <?php echo json_encode($summary['expenses']); ?>,
      <?php echo json_encode($summary['incomes']); ?>
    );
  });
</script>