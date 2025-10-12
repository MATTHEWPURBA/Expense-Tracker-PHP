/**
 * API Client
 * 
 * Centralized API client for all HTTP requests
 * Automatically logs all requests/responses for debugging
 * 
 * @package ExpenseTracker
 */

class ApiClient {
    constructor(options = {}) {
        this.baseUrl = options.baseUrl || '/api.php';
        this.debug = options.debug !== undefined ? options.debug : true;
        this.requestLog = [];
        this.maxLogSize = 100; // Keep last 100 requests
    }

    /**
     * Make an API request
     */
    async request(method, endpoint, data = null, options = {}) {
        const url = `${this.baseUrl}${endpoint}${this.debug ? '?debug=true' : ''}`;
        const startTime = performance.now();
        
        const config = {
            method: method.toUpperCase(),
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            credentials: 'same-origin' // Include cookies for session
        };

        if (this.debug) {
            config.headers['X-Debug'] = 'true';
        }

        // Add body for POST, PUT, PATCH
        if (data && ['POST', 'PUT', 'PATCH'].includes(config.method)) {
            config.body = JSON.stringify(data);
        }

        // Log request
        const requestInfo = {
            id: this.generateRequestId(),
            timestamp: new Date().toISOString(),
            method: config.method,
            endpoint: endpoint,
            url: url,
            data: data,
            headers: config.headers
        };

        try {
            console.group(`🌐 API Request: ${config.method} ${endpoint}`);
            console.log('📤 Request:', requestInfo);

            const response = await fetch(url, config);
            const endTime = performance.now();
            const responseTime = (endTime - startTime).toFixed(2);

            let responseData;
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                responseData = await response.json();
            } else {
                responseData = await response.text();
            }

            // Log response
            const responseInfo = {
                status: response.status,
                statusText: response.statusText,
                ok: response.ok,
                responseTime: `${responseTime}ms`,
                data: responseData,
                headers: this.headersToObject(response.headers)
            };

            console.log('📥 Response:', responseInfo);

            // Log debug info if available
            if (responseData._debug) {
                console.group('🐛 Debug Information');
                console.log('Route:', responseData._debug.route);
                console.log('Controller:', responseData._debug.controller);
                
                if (responseData._debug.queries && responseData._debug.queries.list) {
                    console.group(`💾 Database Queries (${responseData._debug.queries.count})`);
                    responseData._debug.queries.list.forEach((query, index) => {
                        console.group(`Query ${index + 1} - ${query.execution_time}`);
                        console.log('Model:', query.model);
                        console.log('Table:', query.table);
                        console.log('SQL:', query.query);
                        console.log('Params:', query.params);
                        console.groupEnd();
                    });
                    console.groupEnd();
                }
                
                console.log('Performance:', responseData._debug.performance);
                console.log('Memory:', responseData._debug.memory);
                console.groupEnd();
            }

            console.groupEnd();

            // Store in request log
            this.addToLog({
                request: requestInfo,
                response: responseInfo
            });

            // Handle response
            if (!response.ok) {
                throw new ApiError(
                    responseData.error || 'Request failed',
                    response.status,
                    responseData
                );
            }

            return responseData;
        } catch (error) {
            console.error('❌ API Error:', error);
            console.groupEnd();

            // Store error in log
            this.addToLog({
                request: requestInfo,
                error: {
                    message: error.message,
                    stack: error.stack
                }
            });

            throw error;
        }
    }

    /**
     * GET request
     */
    async get(endpoint, options = {}) {
        return this.request('GET', endpoint, null, options);
    }

    /**
     * POST request
     */
    async post(endpoint, data, options = {}) {
        return this.request('POST', endpoint, data, options);
    }

    /**
     * PUT request
     */
    async put(endpoint, data, options = {}) {
        return this.request('PUT', endpoint, data, options);
    }

    /**
     * PATCH request
     */
    async patch(endpoint, data, options = {}) {
        return this.request('PATCH', endpoint, data, options);
    }

    /**
     * DELETE request
     */
    async delete(endpoint, options = {}) {
        return this.request('DELETE', endpoint, null, options);
    }

    /**
     * Add request to log
     */
    addToLog(entry) {
        this.requestLog.unshift(entry);
        
        // Keep only last N requests
        if (this.requestLog.length > this.maxLogSize) {
            this.requestLog = this.requestLog.slice(0, this.maxLogSize);
        }

        // Save to sessionStorage for debugging
        try {
            sessionStorage.setItem('api_request_log', JSON.stringify(this.requestLog));
        } catch (e) {
            // Ignore if storage is full
        }
    }

    /**
     * Get request log
     */
    getLog() {
        return this.requestLog;
    }

    /**
     * Clear request log
     */
    clearLog() {
        this.requestLog = [];
        sessionStorage.removeItem('api_request_log');
    }

    /**
     * Export log as JSON file
     */
    exportLog() {
        const blob = new Blob([JSON.stringify(this.requestLog, null, 2)], {
            type: 'application/json'
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `api-log-${new Date().toISOString()}.json`;
        a.click();
        URL.revokeObjectURL(url);
    }

    /**
     * Generate unique request ID
     */
    generateRequestId() {
        return `req_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    }

    /**
     * Convert Headers object to plain object
     */
    headersToObject(headers) {
        const obj = {};
        for (const [key, value] of headers.entries()) {
            obj[key] = value;
        }
        return obj;
    }

    /**
     * Enable/disable debug mode
     */
    setDebug(enabled) {
        this.debug = enabled;
        console.log(`🐛 Debug mode ${enabled ? 'enabled' : 'disabled'}`);
    }

    /**
     * Show debug panel in browser
     */
    showDebugPanel() {
        if (window.apiDebugPanel) {
            window.apiDebugPanel.style.display = 'block';
            return;
        }

        const panel = document.createElement('div');
        panel.id = 'apiDebugPanel';
        panel.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 400px;
            max-height: 600px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            z-index: 10000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        `;

        panel.innerHTML = `
            <div style="background: #667eea; color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
                <strong>🐛 API Debug Log</strong>
                <div>
                    <button onclick="api.exportLog()" style="background: white; color: #667eea; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; margin-right: 5px;">Export</button>
                    <button onclick="api.clearLog(); this.parentElement.parentElement.parentElement.remove()" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">Close</button>
                </div>
            </div>
            <div style="padding: 15px; overflow-y: auto; flex: 1;">
                <div id="debugLogContent"></div>
            </div>
        `;

        document.body.appendChild(panel);
        window.apiDebugPanel = panel;

        this.updateDebugPanel();
    }

    /**
     * Update debug panel content
     */
    updateDebugPanel() {
        const content = document.getElementById('debugLogContent');
        if (!content) return;

        content.innerHTML = this.requestLog.slice(0, 20).map(entry => `
            <div style="background: #f9fafb; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-size: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <strong style="color: #667eea;">${entry.request.method} ${entry.request.endpoint}</strong>
                    <span style="color: ${entry.response?.ok ? '#10b981' : '#ef4444'};">
                        ${entry.response?.status || 'Error'}
                    </span>
                </div>
                <div style="color: #6b7280; font-size: 11px;">
                    ${new Date(entry.request.timestamp).toLocaleTimeString()} - 
                    ${entry.response?.responseTime || 'N/A'}
                </div>
            </div>
        `).join('');
    }
}

/**
 * Custom API Error class
 */
class ApiError extends Error {
    constructor(message, status, data) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.data = data;
    }
}

// Create global API client instance
const api = new ApiClient({ debug: true });

// Add to window for easy access
window.api = api;
window.ApiClient = ApiClient;
window.ApiError = ApiError;

// Add keyboard shortcut to show debug panel (Ctrl+Shift+D)
document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.shiftKey && e.key === 'D') {
        e.preventDefault();
        api.showDebugPanel();
    }
});

console.log('✅ API Client loaded. Press Ctrl+Shift+D to show debug panel.');
console.log('📖 Access API client via: window.api');

