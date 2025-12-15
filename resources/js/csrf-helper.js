/**
 * CSRF Token Helper for AJAX requests
 * Provides utilities for handling CSRF tokens in Laravel applications
 */

class CSRFHelper {
    constructor() {
        this.token = null;
        this.init();
    }

    /**
     * Initialize CSRF helper
     */
    init() {
        this.loadToken();
        this.setupAxiosDefaults();
        this.setupFetchDefaults();
    }

    /**
     * Load CSRF token from meta tag
     */
    loadToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            this.token = metaTag.getAttribute('content');
        } else {
            console.warn('CSRF token meta tag not found');
        }
    }

    /**
     * Get current CSRF token
     */
    getToken() {
        if (!this.token) {
            this.loadToken();
        }
        return this.token;
    }

    /**
     * Refresh CSRF token from server
     */
    async refreshToken() {
        try {
            const response = await fetch('/csrf-token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.token = data.token;
                this.updateMetaTag();
                return this.token;
            }
        } catch (error) {
            console.error('Failed to refresh CSRF token:', error);
        }
        return null;
    }

    /**
     * Update meta tag with new token
     */
    updateMetaTag() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag && this.token) {
            metaTag.setAttribute('content', this.token);
        }
    }

    /**
     * Setup Axios defaults (if Axios is available)
     */
    setupAxiosDefaults() {
        if (typeof window.axios !== 'undefined') {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = this.getToken();
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        }
    }

    /**
     * Setup fetch defaults by creating a wrapper
     */
    setupFetchDefaults() {
        // Store original fetch
        const originalFetch = window.fetch;
        
        // Create wrapper
        window.fetch = (url, options = {}) => {
            // Ensure headers object exists
            options.headers = options.headers || {};
            
            // Add CSRF token for POST, PUT, PATCH, DELETE requests
            const method = (options.method || 'GET').toUpperCase();
            if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
                options.headers['X-CSRF-TOKEN'] = this.getToken();
                options.headers['X-Requested-With'] = 'XMLHttpRequest';
            }
            
            // Call original fetch
            return originalFetch(url, options);
        };
    }

    /**
     * Create headers object with CSRF token
     */
    getHeaders(additionalHeaders = {}) {
        return {
            'X-CSRF-TOKEN': this.getToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...additionalHeaders
        };
    }

    /**
     * Make authenticated fetch request
     */
    async fetch(url, options = {}) {
        const defaultOptions = {
            headers: this.getHeaders(options.headers || {})
        };

        const mergedOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...(options.headers || {})
            }
        };

        try {
            const response = await fetch(url, mergedOptions);
            
            // Handle CSRF token mismatch
            if (response.status === 419) {
                console.warn('CSRF token mismatch, refreshing token...');
                await this.refreshToken();
                
                // Retry request with new token
                mergedOptions.headers['X-CSRF-TOKEN'] = this.getToken();
                return await fetch(url, mergedOptions);
            }
            
            return response;
        } catch (error) {
            console.error('Fetch request failed:', error);
            throw error;
        }
    }

    /**
     * Make authenticated POST request
     */
    async post(url, data = {}, options = {}) {
        return this.fetch(url, {
            method: 'POST',
            body: JSON.stringify(data),
            ...options
        });
    }

    /**
     * Make authenticated PUT request
     */
    async put(url, data = {}, options = {}) {
        return this.fetch(url, {
            method: 'PUT',
            body: JSON.stringify(data),
            ...options
        });
    }

    /**
     * Make authenticated PATCH request
     */
    async patch(url, data = {}, options = {}) {
        return this.fetch(url, {
            method: 'PATCH',
            body: JSON.stringify(data),
            ...options
        });
    }

    /**
     * Make authenticated DELETE request
     */
    async delete(url, options = {}) {
        return this.fetch(url, {
            method: 'DELETE',
            ...options
        });
    }

    /**
     * Handle form submission with CSRF token
     */
    async submitForm(form, options = {}) {
        const formData = new FormData(form);
        const url = form.action || window.location.href;
        const method = (form.method || 'POST').toUpperCase();

        // Convert FormData to JSON if needed
        if (options.json !== false) {
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            return this.fetch(url, {
                method: method,
                body: JSON.stringify(data),
                ...options
            });
        } else {
            // Use FormData directly
            const headers = {
                'X-CSRF-TOKEN': this.getToken(),
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.headers || {})
            };
            
            // Don't set Content-Type for FormData, let browser set it
            delete headers['Content-Type'];
            
            return this.fetch(url, {
                method: method,
                body: formData,
                headers: headers,
                ...options
            });
        }
    }

    /**
     * Validate current token
     */
    async validateToken() {
        try {
            const response = await this.post('/validate-csrf', {});
            return response.ok;
        } catch (error) {
            return false;
        }
    }
}

// Create global instance
window.csrfHelper = new CSRFHelper();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CSRFHelper;
}

// Auto-refresh token on page visibility change
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        window.csrfHelper.refreshToken();
    }
});

// Refresh token periodically (every 30 minutes)
setInterval(() => {
    window.csrfHelper.refreshToken();
}, 30 * 60 * 1000);

console.log('CSRF Helper initialized');
