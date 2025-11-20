// AI Advisor Functions
let chatHistory = [];
let currentSummaryYear = new Date().getFullYear();
let currentSummaryMonth = new Date().getMonth() + 1; // JavaScript months are 0-indexed

// Load insights on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('insightsContainer')) {
        loadInsights();
        loadMonthlySummary(currentSummaryYear, currentSummaryMonth);
    }
});

function loadInsights() {
    fetch('/ai/insights')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayInsights(data.data);
            } else {
                showInsightsError();
            }
        })
        .catch(error => {
            console.error('Error loading insights:', error);
            showInsightsError();
        });
}

function displayInsights(insights) {
    // Spending Insights
    if (insights.spending_insights) {
        const spendingContent = document.getElementById('spending-insights-content');
        spendingContent.innerHTML = formatInsightPoints(insights.spending_insights.points);
        
        const spendingTime = document.getElementById('spending-insights-time');
        spendingTime.textContent = `Updated: ${formatTime(insights.spending_insights.updated_at)}`;
    }

    // Alerts
    if (insights.alerts) {
        const alertsContent = document.getElementById('alerts-content');
        alertsContent.innerHTML = formatInsightPoints(insights.alerts.points, 'warning');
        
        const alertsTime = document.getElementById('alerts-time');
        alertsTime.textContent = `Updated: ${formatTime(insights.alerts.updated_at)}`;
    }

    // Tips
    if (insights.tips) {
        const tipsContent = document.getElementById('tips-content');
        tipsContent.innerHTML = formatInsightPoints(insights.tips.points, 'success');
        
        const tipsTime = document.getElementById('tips-time');
        tipsTime.textContent = `Updated: ${formatTime(insights.tips.updated_at)}`;
    }
}

function formatInsightPoints(points, type = 'info') {
    if (!points || points.length === 0) {
        return '<p class="text-muted">No insights available yet.</p>';
    }

    let html = '<ul class="list-unstyled mb-0">';
    points.forEach(point => {
        let icon = '•';
        if (type === 'warning') icon = '⚠️';
        if (type === 'success') icon = '✓';
        
        html += `<li class="mb-2">${icon} ${escapeHtml(point)}</li>`;
    });
    html += '</ul>';
    
    return html;
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diffMinutes = Math.floor((now - date) / 60000);
    
    if (diffMinutes < 1) return 'just now';
    if (diffMinutes < 60) return `${diffMinutes}min ago`;
    
    const diffHours = Math.floor(diffMinutes / 60);
    if (diffHours < 24) return `${diffHours}h ago`;
    
    return date.toLocaleDateString();
}

function showInsightsError() {
    const errorHtml = '<p class="text-danger">Failed to load insights. Please try again.</p>';
    document.getElementById('spending-insights-content').innerHTML = errorHtml;
    document.getElementById('alerts-content').innerHTML = errorHtml;
    document.getElementById('tips-content').innerHTML = errorHtml;
}

function refreshInsights() {
    // Show loading state
    const loadingHtml = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    document.getElementById('spending-insights-content').innerHTML = loadingHtml;
    document.getElementById('alerts-content').innerHTML = loadingHtml.replace('text-primary', 'text-warning');
    document.getElementById('tips-content').innerHTML = loadingHtml.replace('text-primary', 'text-success');

    // Force refresh by clearing cache
    const csrfToken = window.csrfToken || document.querySelector('input[name="csrf_token"]')?.value || '';
    
    fetch('/ai/insights/refresh', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `token=${encodeURIComponent(csrfToken)}`
    })
    .then(response => {
        // Always get text first for debugging
        return response.text().then(text => {
            console.log('Server response status:', response.status);
            console.log('Server response body:', text.substring(0, 500));
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
            }
            
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                console.error('Response was:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        // Update CSRF token if provided
        if (data.csrfToken) {
            window.csrfToken = data.csrfToken;
        }
        
        if (data.success) {
            displayInsights(data.data);
        } else {
            showInsightsError();
        }
    })
    .catch(error => {
        console.error('Error refreshing insights:', error);
        showInsightsError();
    });
}

// Chat Functions
function sendChatMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Add user message to chat
    addMessageToChat('user', message);
    input.value = '';
    
    // Show loading
    addMessageToChat('loading', 'Thinking...');
    
    // Send to server
    const csrfToken = window.csrfToken || document.querySelector('input[name="csrf_token"]')?.value || '';
    console.log('Sending chat with token:', csrfToken.substring(0, 10) + '...');
    
    fetch('/ai/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `message=${encodeURIComponent(message)}&token=${encodeURIComponent(csrfToken)}`
    })
    .then(response => {
        return response.text().then(text => {
            console.log('Chat response status:', response.status);
            console.log('Chat response body:', text.substring(0, 500));
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
            }
            
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse chat JSON:', e);
                throw new Error('Invalid JSON response from chat');
            }
        });
    })
    .then(data => {
        // Update CSRF token if provided
        if (data.csrfToken) {
            window.csrfToken = data.csrfToken;
        }
        
        // Remove loading message
        const loadingMsg = document.querySelector('.message-loading');
        if (loadingMsg) loadingMsg.remove();
        
        if (data.success) {
            addMessageToChat('model', data.message);
        } else {
            addMessageToChat('error', data.message || 'Sorry, something went wrong.');
        }
    })
    .catch(error => {
        console.error('Chat error:', error);
        const loadingMsg = document.querySelector('.message-loading');
        if (loadingMsg) loadingMsg.remove();
        addMessageToChat('error', 'Failed to send message. Please try again.');
    });
}

function sendQuickQuestion(question) {
    const input = document.getElementById('chatInput');
    input.value = question;
    sendChatMessage();
}

function addMessageToChat(role, message) {
    const chatMessages = document.getElementById('chatMessages');
    
    // Clear initial message
    const initialMsg = chatMessages.querySelector('.text-center.text-muted');
    if (initialMsg) {
        initialMsg.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `mb-3 ${role === 'user' ? 'text-end' : 'text-start'}`;
    
    if (role === 'loading') {
        messageDiv.classList.add('message-loading');
        messageDiv.innerHTML = `
            <span class="badge bg-secondary">
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                ${escapeHtml(message)}
            </span>
        `;
    } else if (role === 'error') {
        messageDiv.innerHTML = `
            <div class="alert alert-danger d-inline-block" role="alert">
                ${escapeHtml(message)}
            </div>
        `;
    } else {
        const badgeClass = role === 'user' ? 'bg-primary' : 'bg-success';
        const label = role === 'user' ? 'You' : 'AI Advisor';
        
        messageDiv.innerHTML = `
            <div class="d-inline-block text-start" style="max-width: 80%;">
                <small class="text-muted d-block mb-1">${label}</small>
                <div class="card ${role === 'user' ? 'bg-primary text-white' : 'bg-light'}">
                    <div class="card-body py-2 px-3">
                        ${escapeHtml(message).replace(/\n/g, '<br>')}
                    </div>
                </div>
            </div>
        `;
    }
    
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Clear chat history function
function clearChatHistory() {
    if (!confirm('Are you sure you want to clear the entire chat history? This cannot be undone.')) {
        return;
    }

    fetch('/ai/chat/clear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update CSRF token
            if (data.csrfToken) {
                window.csrfToken = data.csrfToken;
            }
            
            // Clear chat display
            document.getElementById('chatMessages').innerHTML = `
                <div class="text-center text-muted py-5">
                    <h5>Chat history cleared!</h5>
                    <p>Ask me anything about your finances!</p>
                </div>
            `;
            
            // Reset chat history array
            chatHistory = [];
            
            // Show success message
            showNotification('Chat history cleared successfully', 'success');
        } else {
            showNotification('Failed to clear chat history', 'danger');
        }
    })
    .catch(error => {
        console.error('Error clearing chat:', error);
        showNotification('Error clearing chat history', 'danger');
    });
}

// Helper function to show notifications
function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
    toast.style.zIndex = '9999';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Monthly Summary Functions
function loadMonthlySummary(year, month) {
    const content = document.getElementById('monthly-summary-content');
    content.innerHTML = '<div class="text-center py-3"><div class="spinner-border" role="status"></div></div>';
    
    fetch(`/ai/summary?year=${year}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                displayMonthlySummary(data.data);
            } else {
                content.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <p>No summary available for ${getMonthName(month)} ${year}</p>
                        <small>Summaries are generated automatically at the end of each month</small>
                    </div>
                `;
            }
            updateMonthNavigation(year, month);
        })
        .catch(error => {
            console.error('Error loading summary:', error);
            content.innerHTML = '<div class="alert alert-danger">Failed to load summary</div>';
        });
}

function displayMonthlySummary(summary) {
    const content = document.getElementById('monthly-summary-content');
    
    if (!summary.is_finalized) {
        content.innerHTML = `
            <div class="alert alert-info">
                <h6>Current Month - In Progress</h6>
                <p class="mb-0">This month is still active. Summary will be generated automatically at month end.</p>
                <small>Total Expenses so far: $${parseFloat(summary.total_expenses).toFixed(2)}</small>
            </div>
        `;
        return;
    }
    
    const balance = parseFloat(summary.total_income) - parseFloat(summary.total_expenses);
    const balanceClass = balance >= 0 ? 'text-success' : 'text-danger';
    
    content.innerHTML = `
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="text-center p-3 bg-light rounded">
                    <small class="text-muted">Income</small>
                    <h4 class="text-success mb-0">$${parseFloat(summary.total_income).toFixed(2)}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-3 bg-light rounded">
                    <small class="text-muted">Expenses</small>
                    <h4 class="text-danger mb-0">$${parseFloat(summary.total_expenses).toFixed(2)}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-3 bg-light rounded">
                    <small class="text-muted">Balance</small>
                    <h4 class="${balanceClass} mb-0">$${balance.toFixed(2)}</h4>
                </div>
            </div>
        </div>
        
        ${summary.top_expense_category ? `
            <div class="alert alert-warning mb-3">
                <strong>Top Spending Category:</strong> ${summary.top_expense_category} - $${parseFloat(summary.top_expense_amount).toFixed(2)}
            </div>
        ` : ''}
        
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">📝 AI Summary</h6>
            </div>
            <div class="card-body">
                <p>${summary.ai_summary || 'No summary generated yet'}</p>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">⚠️ Key Issues Identified</h6>
            </div>
            <div class="card-body">
                <div class="issues-list">${formatTextAsList(summary.key_issues)}</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">💡 Recommendations for Next Month</h6>
            </div>
            <div class="card-body">
                <div class="recommendations-list">${formatTextAsList(summary.recommendations)}</div>
            </div>
        </div>
        
        <small class="text-muted d-block mt-3">
            Generated: ${new Date(summary.created_at).toLocaleDateString()} | 
            Transactions: ${summary.transaction_count}
        </small>
    `;
}

function formatTextAsList(text) {
    if (!text) return '<p class="text-muted">None</p>';
    
    // Split by lines and format as list
    const lines = text.split('\n').filter(line => line.trim());
    return '<ul class="mb-0">' + lines.map(line => {
        // Remove leading dash if present
        const cleanLine = line.trim().replace(/^-\s*/, '');
        return `<li>${cleanLine}</li>`;
    }).join('') + '</ul>';
}

function updateMonthNavigation(year, month) {
    const monthName = getMonthName(month);
    document.getElementById('summaryMonthTitle').textContent = `📊 ${monthName} ${year}`;
    
    // Disable next button if current or future month
    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonth = now.getMonth() + 1;
    const isCurrentOrFuture = (year > currentYear) || (year === currentYear && month >= currentMonth);
    
    document.getElementById('nextMonthBtn').disabled = isCurrentOrFuture;
}

function previousMonth() {
    currentSummaryMonth--;
    if (currentSummaryMonth < 1) {
        currentSummaryMonth = 12;
        currentSummaryYear--;
    }
    loadMonthlySummary(currentSummaryYear, currentSummaryMonth);
}

function nextMonth() {
    currentSummaryMonth++;
    if (currentSummaryMonth > 12) {
        currentSummaryMonth = 1;
        currentSummaryYear++;
    }
    loadMonthlySummary(currentSummaryYear, currentSummaryMonth);
}

function getMonthName(month) {
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                    'July', 'August', 'September', 'October', 'November', 'December'];
    return months[month - 1];
}

// Clear chat when modal is closed (optional)
const chatModal = document.getElementById('chatModal');
if (chatModal) {
    chatModal.addEventListener('hidden.bs.modal', function () {
        // Optional: Clear chat history when modal closes
        // document.getElementById('chatMessages').innerHTML = '<div class="text-center text-muted py-5"><h5>Ask me anything about your finances!</h5><p>Try the quick actions below or type your own question.</p></div>';
    });
}
