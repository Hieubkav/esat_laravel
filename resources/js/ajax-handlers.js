/**
 * AJAX Handlers for Authentication and General Operations
 * Provides utilities for handling AJAX requests with proper error handling and UI feedback
 */

class AjaxHandlers {
    constructor() {
        this.defaultOptions = {
            timeout: 30000, // 30 seconds
            retries: 3,
            retryDelay: 1000 // 1 second
        };
        
        this.init();
    }

    /**
     * Initialize AJAX handlers
     */
    init() {
        this.setupGlobalErrorHandling();
        this.exposeToWindow();
    }

    /**
     * Setup global error handling
     */
    setupGlobalErrorHandling() {
        // Handle unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled promise rejection:', event.reason);
            this.showError('Có lỗi xảy ra. Vui lòng thử lại sau.');
        });
    }

    /**
     * Make authenticated request with retry logic
     */
    async makeRequest(url, options = {}) {
        const mergedOptions = {
            ...this.defaultOptions,
            ...options
        };

        let lastError;
        
        for (let attempt = 1; attempt <= mergedOptions.retries; attempt++) {
            try {
                const response = await this.executeRequest(url, options);
                return response;
            } catch (error) {
                lastError = error;
                
                // Don't retry on client errors (4xx)
                if (error.status && error.status >= 400 && error.status < 500) {
                    throw error;
                }
                
                // Wait before retry
                if (attempt < mergedOptions.retries) {
                    await this.delay(mergedOptions.retryDelay * attempt);
                }
            }
        }
        
        throw lastError;
    }

    /**
     * Execute single request
     */
    async executeRequest(url, options = {}) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), options.timeout || this.defaultOptions.timeout);

        try {
            const response = await window.csrfHelper.fetch(url, {
                ...options,
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                const error = new Error(`HTTP ${response.status}`);
                error.status = response.status;
                error.response = response;
                throw error;
            }

            return response;
        } catch (error) {
            clearTimeout(timeoutId);
            
            if (error.name === 'AbortError') {
                const timeoutError = new Error('Request timeout');
                timeoutError.code = 'TIMEOUT';
                throw timeoutError;
            }
            
            throw error;
        }
    }

    /**
     * Handle authentication requests
     */
    async handleAuth(url, formData, options = {}) {
        const loadingElement = options.loadingElement;
        const errorContainer = options.errorContainer;
        
        try {
            // Show loading state
            if (loadingElement) {
                this.setLoadingState(loadingElement, true);
            }
            
            // Clear previous errors
            if (errorContainer) {
                this.clearErrors(errorContainer);
            }

            const response = await this.makeRequest(url, {
                method: 'POST',
                body: JSON.stringify(formData),
                ...options
            });

            const data = await response.json();

            if (data.success) {
                this.handleAuthSuccess(data, options);
                return data;
            } else {
                this.handleAuthError(data, errorContainer);
                return data;
            }
            
        } catch (error) {
            this.handleRequestError(error, errorContainer);
            throw error;
        } finally {
            // Hide loading state
            if (loadingElement) {
                this.setLoadingState(loadingElement, false);
            }
        }
    }

    /**
     * Handle successful authentication
     */
    handleAuthSuccess(data, options = {}) {
        // Show success message
        this.showSuccess(data.message || 'Thành công!');
        
        // Emit events
        if (options.eventName) {
            window.dispatchEvent(new CustomEvent(options.eventName, {
                detail: data
            }));
        }
        
        // Handle redirect
        if (data.redirect) {
            // Check if we should stay on current page
            if (data.stay_on_page && data.redirect === window.location.href) {
                // Just reload the current page to update auth state
                setTimeout(() => {
                    window.location.reload();
                }, options.redirectDelay || 1000);
            } else {
                // Redirect to specified URL
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, options.redirectDelay || 1000);
            }
        } else if (options.reload) {
            setTimeout(() => {
                window.location.reload();
            }, options.reloadDelay || 1000);
        }
    }

    /**
     * Handle authentication errors
     */
    handleAuthError(data, errorContainer) {
        if (data.errors && errorContainer) {
            this.displayErrors(data.errors, errorContainer);
        } else if (data.message) {
            this.showError(data.message);
        }
    }

    /**
     * Handle request errors
     */
    handleRequestError(error, errorContainer) {
        let message = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
        
        if (error.code === 'TIMEOUT') {
            message = 'Yêu cầu quá thời gian. Vui lòng kiểm tra kết nối mạng.';
        } else if (error.status === 419) {
            message = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.';
        } else if (error.status === 422) {
            message = 'Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.';
        } else if (error.status === 429) {
            message = 'Quá nhiều yêu cầu. Vui lòng thử lại sau.';
        } else if (error.status >= 500) {
            message = 'Lỗi máy chủ. Vui lòng thử lại sau.';
        }
        
        if (errorContainer) {
            this.displayErrors({ general: message }, errorContainer);
        } else {
            this.showError(message);
        }
    }

    /**
     * Set loading state for element
     */
    setLoadingState(element, loading) {
        if (loading) {
            element.disabled = true;
            element.classList.add('loading');
            
            // Add loading spinner if button
            if (element.tagName === 'BUTTON') {
                const originalText = element.textContent;
                element.dataset.originalText = originalText;
                element.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Đang xử lý...
                `;
            }
        } else {
            element.disabled = false;
            element.classList.remove('loading');
            
            // Restore original text if button
            if (element.tagName === 'BUTTON' && element.dataset.originalText) {
                element.textContent = element.dataset.originalText;
                delete element.dataset.originalText;
            }
        }
    }

    /**
     * Display validation errors
     */
    displayErrors(errors, container) {
        this.clearErrors(container);
        
        Object.keys(errors).forEach(field => {
            const errorElement = container.querySelector(`[data-error="${field}"]`) || 
                                container.querySelector(`#${field}-error`) ||
                                container.querySelector(`.${field}-error`);
            
            if (errorElement) {
                errorElement.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                errorElement.style.display = 'block';
            }
        });
    }

    /**
     * Clear all errors in container
     */
    clearErrors(container) {
        const errorElements = container.querySelectorAll('[data-error], [id$="-error"], [class*="-error"]');
        errorElements.forEach(element => {
            element.textContent = '';
            element.style.display = 'none';
        });
    }

    /**
     * Show success notification
     */
    showSuccess(message) {
        this.showNotification('success', message);
    }

    /**
     * Show error notification
     */
    showError(message) {
        this.showNotification('error', message);
    }

    /**
     * Show notification
     */
    showNotification(type, message) {
        // Use existing notification system if available
        if (window.authModal && typeof window.authModal.showNotification === 'function') {
            window.authModal.showNotification(type, message);
            return;
        }
        
        // Fallback to simple alert
        if (type === 'error') {
            alert('Lỗi: ' + message);
        } else {
            alert(message);
        }
    }

    /**
     * Delay utility
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    /**
     * Expose methods to window
     */
    exposeToWindow() {
        window.ajaxHandlers = {
            auth: (url, data, options) => this.handleAuth(url, data, options),
            request: (url, options) => this.makeRequest(url, options),
            setLoading: (element, loading) => this.setLoadingState(element, loading),
            showSuccess: (message) => this.showSuccess(message),
            showError: (message) => this.showError(message),
            clearErrors: (container) => this.clearErrors(container),
            displayErrors: (errors, container) => this.displayErrors(errors, container)
        };
    }
}

// Create global instance
window.ajaxHandlersInstance = new AjaxHandlers();

// Helper functions for common operations
window.submitAuthForm = async function(form, options = {}) {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    return window.ajaxHandlers.auth(form.action, data, {
        loadingElement: form.querySelector('[type="submit"]'),
        errorContainer: form,
        eventName: options.eventName,
        ...options
    });
};

console.log('AJAX Handlers initialized');
