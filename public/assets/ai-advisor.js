// AI Advisor Functions
let chatHistory = [];

// Load insights on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('insightsContainer')) {
        loadInsights();
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

// Clear chat when modal is closed (optional)
const chatModal = document.getElementById('chatModal');
if (chatModal) {
    chatModal.addEventListener('hidden.bs.modal', function () {
        // Optional: Clear chat history when modal closes
        // document.getElementById('chatMessages').innerHTML = '<div class="text-center text-muted py-5"><h5>Ask me anything about your finances!</h5><p>Try the quick actions below or type your own question.</p></div>';
    });
}
