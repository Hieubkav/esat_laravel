/**
 * Fallback Detection and Graceful Degradation
 * Ensures the application works when JavaScript is disabled or fails
 */

class FallbackDetection {
    constructor() {
        // Throttle network error banner
        this.lastNetworkErrorAt = 0;
        this.networkBanner = null;
        this.init();
    }

    init() {
        this.detectJavaScriptSupport();
        this.setupErrorHandling();
        this.setupFallbackMechanisms();
        this.setupLogoutHandling();
        this.testCriticalFeatures();
    }

    /**
     * Detect JavaScript support and mark body
     */
    detectJavaScriptSupport() {
        // Remove no-js class and add js class
        document.documentElement.classList.remove('no-js');
        document.documentElement.classList.add('js');
        
        // Add data attribute for CSS targeting
        document.body.setAttribute('data-js-enabled', 'true');
    }

    /**
     * Setup global error handling
     */
    setupErrorHandling() {
        // Handle JavaScript errors
        window.addEventListener('error', (event) => {
            console.error('JavaScript Error:', event.error);
            this.handleJavaScriptFailure();
        });

        // Handle unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled Promise Rejection:', event.reason);
            this.handleJavaScriptFailure();
        });

        // Handle Alpine.js initialization failure
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js initialized successfully');
        });

        // Fallback if Alpine.js doesn't initialize within 5 seconds
        setTimeout(() => {
            if (!window.Alpine) {
                console.warn('Alpine.js failed to initialize, enabling fallbacks');
                this.enableFallbackMode();
            }
        }, 5000);
    }

    /**
     * Setup fallback mechanisms
     */
    setupFallbackMechanisms() {
        // Convert modal triggers to regular links if Alpine.js fails
        this.setupModalFallbacks();
        
        // Setup form fallbacks
        this.setupFormFallbacks();
        
        // Setup AJAX fallbacks
        this.setupAjaxFallbacks();
    }

    /**
     * Setup modal fallbacks
     */
    setupModalFallbacks() {
        // Find all modal trigger buttons
        const modalTriggers = document.querySelectorAll('[\\@click*="auth-modal-open"]');
        
        modalTriggers.forEach(trigger => {
            // Add fallback onclick handler
            if (!trigger.onclick) {
                trigger.onclick = function() {
                    if (!window.Alpine || !window.Alpine.version) {
                        // Redirect to login page if Alpine.js is not available
                        window.location.href = '/khach-hang/dang-nhap';
                        return false;
                    }
                };
            }
        });
    }

    /**
     * Setup form fallbacks
     */
    setupFormFallbacks() {
        // Find forms that use AJAX submission
        const ajaxForms = document.querySelectorAll('form[\\@submit\\.prevent]');
        
        ajaxForms.forEach(form => {
            // Add fallback submit handler
            form.addEventListener('submit', (event) => {
                // If AJAX handlers are not available, allow normal form submission
                if (!window.csrfHelper || !window.ajaxHandlers) {
                    console.warn('AJAX handlers not available, using normal form submission');
                    // Remove prevent default to allow normal submission
                    event.stopImmediatePropagation();
                    return true;
                }
            });
        });
    }

    /**
     * Setup AJAX fallbacks
     */
    setupAjaxFallbacks() {
        // Override fetch if it fails
        const originalFetch = window.fetch;

        window.fetch = function(...args) {
            return originalFetch.apply(this, args).then(response => {
                // Log successful responses during logout
                if (window.isLoggingOut) {
                    console.log('Fetch success during logout:', args[0], response.status);
                }
                return response;
            }).catch(error => {
                console.error('Fetch failed:', error);
                console.log('Request URL:', args[0]);
                console.log('Error details:', {
                    name: error.name,
                    message: error.message,
                    status: error.status,
                    isLoggingOut: window.isLoggingOut
                });

                // Don't show network error for authentication/authorization issues
                if (error.status === 401 || error.status === 403 || error.status === 419) {
                    console.log('Authentication/authorization error, not showing network error dialog');
                    throw error; // Re-throw for proper handling
                }

                // Don't show error during logout process
                if (window.isLoggingOut) {
                    console.log('Logout in progress, suppressing network error dialog');
                    throw error;
                }

                // Check if this is a network error (not HTTP error)
                const isNetworkError = error.name === 'TypeError' ||
                                     error.message.includes('Failed to fetch') ||
                                     error.message.includes('NetworkError') ||
                                     error.message.includes('fetch');

                if (isNetworkError) {
                    const message = 'Kết nối mạng có vấn đề. Vui lòng tải lại trang.';
                    this.showNetworkErrorBanner(message);
                } else {
                    console.log('Not showing dialog for error:', error);
                }
                
                throw error;
            });
        };
    }

    /**
     * Setup logout handling to prevent false network errors
     */
    setupLogoutHandling() {
        // Listen for logout events
        document.addEventListener('livewire:init', () => {
            if (window.Livewire) {
                window.Livewire.on('customer-logged-out', () => {
                    // Reset logout flag after a short delay
                    setTimeout(() => {
                        window.isLoggingOut = false;
                    }, 2000);
                });
            }
        });

        // Also listen for page unload to reset flag
        window.addEventListener('beforeunload', () => {
            window.isLoggingOut = false;
        });
    }

    /**
     * Test critical features
     */
    testCriticalFeatures() {
        const tests = [
            this.testLocalStorage,
            this.testSessionStorage,
            this.testFetch,
            this.testCSRFToken,
            this.testAlpineJS
        ];

        tests.forEach(test => {
            try {
                test.call(this);
            } catch (error) {
                console.warn('Feature test failed:', test.name, error);
            }
        });
    }

    /**
     * Test localStorage availability
     */
    testLocalStorage() {
        try {
            const testKey = '__test__';
            localStorage.setItem(testKey, 'test');
            localStorage.removeItem(testKey);
            return true;
        } catch (error) {
            console.warn('localStorage not available');
            return false;
        }
    }

    /**
     * Test sessionStorage availability
     */
    testSessionStorage() {
        try {
            const testKey = '__test__';
            sessionStorage.setItem(testKey, 'test');
            sessionStorage.removeItem(testKey);
            return true;
        } catch (error) {
            console.warn('sessionStorage not available');
            return false;
        }
    }

    /**
     * Test fetch API availability
     */
    testFetch() {
        if (!window.fetch) {
            console.warn('Fetch API not available');
            return false;
        }
        return true;
    }

    /**
     * Test CSRF token availability
     */
    testCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token || !token.content) {
            console.warn('CSRF token not found');
            return false;
        }
        return true;
    }

    /**
     * Test Alpine.js availability
     */
    testAlpineJS() {
        if (!window.Alpine) {
            console.warn('Alpine.js not available');
            return false;
        }
        return true;
    }

    /**
     * Handle JavaScript failure
     */
    handleJavaScriptFailure() {
        // Mark body as having JS errors
        document.body.setAttribute('data-js-error', 'true');
        
        // Show fallback message
        this.showFallbackMessage();
        
        // Enable fallback mode
        this.enableFallbackMode();
    }

    /**
     * Enable fallback mode
     */
    enableFallbackMode() {
        document.body.classList.add('fallback-mode');
        
        // Convert all modal triggers to regular links
        const modalTriggers = document.querySelectorAll('[data-modal-trigger]');
        modalTriggers.forEach(trigger => {
            const link = document.createElement('a');
            link.href = '/khach-hang/dang-nhap';
            link.className = trigger.className;
            link.innerHTML = trigger.innerHTML;
            trigger.parentNode.replaceChild(link, trigger);
        });
        
        // Remove Alpine.js directives from forms
        const alpineForms = document.querySelectorAll('form[x-data]');
        alpineForms.forEach(form => {
            form.removeAttribute('x-data');
            form.removeAttribute('@submit.prevent');
        });
    }

    /**
     * Show fallback message
     */
    showFallbackMessage() {
        const message = document.createElement('div');
        message.className = 'fixed top-0 left-0 right-0 bg-yellow-100 border-b border-yellow-300 p-3 text-center text-sm text-yellow-800 z-50';
        message.innerHTML = `
            <strong>Chế độ tương thích:</strong> 
            Một số tính năng có thể không hoạt động đầy đủ. 
            <a href="javascript:window.location.reload()" class="underline">Tải lại trang</a>
        `;
        
        document.body.insertBefore(message, document.body.firstChild);
        
        // Auto-hide after 10 seconds
        setTimeout(() => {
            if (message.parentNode) {
                message.parentNode.removeChild(message);
            }
        }, 10000);
    }

    /**
     * Non-blocking network error banner (throttled)
     */
    showNetworkErrorBanner(text) {
        const now = Date.now();
        // Chỉ hiển thị tối đa 1 lần mỗi 60s
        if (now - this.lastNetworkErrorAt < 60000) return;
        this.lastNetworkErrorAt = now;

        // Nếu đã có banner, cập nhật nội dung và gia hạn tự ẩn
        if (this.networkBanner) {
            this.networkBanner.querySelector('[data-msg]').textContent = text;
        } else {
            const banner = document.createElement('div');
            banner.className = 'fixed bottom-4 left-1/2 -translate-x-1/2 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-700 shadow-xl rounded-lg px-4 py-3 z-[1000]';
            banner.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-wifi text-red-500"></i>
                    <span data-msg>${text}</span>
                    <button type="button" class="ml-3 text-red-600 hover:text-red-700 underline" data-reload>Tải lại</button>
                    <button type="button" class="ml-2 text-gray-500 hover:text-gray-700" data-close>Đóng</button>
                </div>`;
            document.body.appendChild(banner);
            this.networkBanner = banner;

            banner.querySelector('[data-reload]').addEventListener('click', () => {
                window.location.reload();
            });
            banner.querySelector('[data-close]').addEventListener('click', () => {
                banner.remove();
                this.networkBanner = null;
            });
        }

        // Tự ẩn sau 6s nếu người dùng không tương tác
        clearTimeout(this._networkHideTimer);
        this._networkHideTimer = setTimeout(() => {
            if (this.networkBanner) {
                this.networkBanner.remove();
                this.networkBanner = null;
            }
        }, 6000);
    }

    /**
     * Check if fallback mode is needed
     */
    static needsFallback() {
        return !window.Alpine || 
               !window.fetch || 
               !document.querySelector('meta[name="csrf-token"]');
    }
}

// Initialize fallback detection
document.addEventListener('DOMContentLoaded', () => {
    window.fallbackDetection = new FallbackDetection();
});

// Add CSS class for no-js fallback
document.documentElement.classList.add('no-js');

console.log('Fallback Detection loaded');
