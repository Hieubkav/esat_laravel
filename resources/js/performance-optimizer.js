/**
 * Performance Optimizer for Authentication Modal
 * Optimizes loading times, animations, and resource usage
 */

class PerformanceOptimizer {
    constructor() {
        this.metrics = {
            startTime: performance.now(),
            loadTime: null,
            modalOpenTime: null,
            firstInteraction: null
        };
        
        this.init();
    }

    init() {
        this.setupPerformanceMonitoring();
        this.optimizeAnimations();
        this.setupLazyLoading();
        this.optimizeEventListeners();
        this.setupResourceHints();
    }

    /**
     * Setup performance monitoring
     */
    setupPerformanceMonitoring() {
        // Mark page load complete
        window.addEventListener('load', () => {
            this.metrics.loadTime = performance.now() - this.metrics.startTime;
            this.logMetric('Page Load Time', this.metrics.loadTime);
        });

        // Monitor modal performance
        window.addEventListener('auth-modal-open', () => {
            performance.mark('modal-open-start');
            this.metrics.modalOpenTime = performance.now();
        });

        // Monitor first user interaction
        ['click', 'keydown', 'touchstart'].forEach(event => {
            document.addEventListener(event, () => {
                if (!this.metrics.firstInteraction) {
                    this.metrics.firstInteraction = performance.now() - this.metrics.startTime;
                    this.logMetric('First Interaction', this.metrics.firstInteraction);
                }
            }, { once: true, passive: true });
        });

        // Monitor Core Web Vitals
        this.monitorCoreWebVitals();
    }

    /**
     * Monitor Core Web Vitals
     */
    monitorCoreWebVitals() {
        // Largest Contentful Paint (LCP)
        if ('PerformanceObserver' in window) {
            try {
                const lcpObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    this.logMetric('LCP', lastEntry.startTime);
                });
                lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });

                // First Input Delay (FID)
                const fidObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    entries.forEach(entry => {
                        this.logMetric('FID', entry.processingStart - entry.startTime);
                    });
                });
                fidObserver.observe({ entryTypes: ['first-input'] });

                // Cumulative Layout Shift (CLS)
                let clsValue = 0;
                const clsObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    entries.forEach(entry => {
                        if (!entry.hadRecentInput) {
                            clsValue += entry.value;
                        }
                    });
                    this.logMetric('CLS', clsValue);
                });
                clsObserver.observe({ entryTypes: ['layout-shift'] });
            } catch (error) {
                console.warn('Performance Observer not fully supported:', error);
            }
        }
    }

    /**
     * Optimize animations based on user preferences and device capabilities
     */
    optimizeAnimations() {
        // Check for reduced motion preference
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        if (prefersReducedMotion) {
            document.documentElement.style.setProperty('--animation-duration', '0.01ms');
            document.documentElement.style.setProperty('--transition-duration', '0.01ms');
        }

        // Optimize for low-end devices
        if (this.isLowEndDevice()) {
            document.documentElement.classList.add('low-end-device');
            // Reduce animation complexity
            document.documentElement.style.setProperty('--animation-duration', '150ms');
        }

        // Use requestAnimationFrame for smooth animations
        this.setupRAFOptimization();
    }

    /**
     * Check if device is low-end
     */
    isLowEndDevice() {
        // Check for device memory (if available)
        if ('deviceMemory' in navigator && navigator.deviceMemory < 4) {
            return true;
        }

        // Check for hardware concurrency
        if ('hardwareConcurrency' in navigator && navigator.hardwareConcurrency < 4) {
            return true;
        }

        // Check for connection speed
        if ('connection' in navigator) {
            const connection = navigator.connection;
            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                return true;
            }
        }

        return false;
    }

    /**
     * Setup RequestAnimationFrame optimization
     */
    setupRAFOptimization() {
        // Throttle scroll events
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            if (scrollTimeout) return;
            
            scrollTimeout = requestAnimationFrame(() => {
                // Handle scroll-based animations here
                scrollTimeout = null;
            });
        }, { passive: true });

        // Optimize resize events
        let resizeTimeout;
        window.addEventListener('resize', () => {
            if (resizeTimeout) return;
            
            resizeTimeout = requestAnimationFrame(() => {
                // Handle resize-based updates here
                resizeTimeout = null;
            });
        }, { passive: true });
    }

    /**
     * Setup lazy loading for non-critical resources
     */
    setupLazyLoading() {
        // Lazy load modal content
        const modalContainer = document.querySelector('[x-data*="authModal"]');
        if (modalContainer && 'IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.preloadModalAssets();
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            observer.observe(modalContainer);
        }

        // Preload critical resources on user interaction
        ['mouseenter', 'touchstart', 'focus'].forEach(event => {
            document.addEventListener(event, () => {
                this.preloadCriticalResources();
            }, { once: true, passive: true });
        });
    }

    /**
     * Preload modal assets
     */
    preloadModalAssets() {
        // Preload authentication endpoints
        if ('fetch' in window) {
            // Warm up DNS for auth endpoints
            const link = document.createElement('link');
            link.rel = 'dns-prefetch';
            link.href = window.location.origin;
            document.head.appendChild(link);
        }
    }

    /**
     * Preload critical resources
     */
    preloadCriticalResources() {
        // Preload CSRF token refresh
        if (window.csrfHelper && typeof window.csrfHelper.refreshToken === 'function') {
            // Refresh token in background
            window.csrfHelper.refreshToken().catch(() => {
                // Ignore errors in background refresh
            });
        }
    }

    /**
     * Optimize event listeners
     */
    optimizeEventListeners() {
        // Use passive listeners where possible
        const passiveEvents = ['scroll', 'touchstart', 'touchmove', 'wheel'];
        
        passiveEvents.forEach(eventType => {
            // Override addEventListener for these events to default to passive
            const originalAddEventListener = EventTarget.prototype.addEventListener;
            EventTarget.prototype.addEventListener = function(type, listener, options) {
                if (passiveEvents.includes(type) && typeof options !== 'object') {
                    options = { passive: true };
                } else if (typeof options === 'object' && options.passive === undefined) {
                    options.passive = true;
                }
                
                return originalAddEventListener.call(this, type, listener, options);
            };
        });

        // Debounce frequent events
        this.setupEventDebouncing();
    }

    /**
     * Setup event debouncing
     */
    setupEventDebouncing() {
        // Debounce input events
        const debounce = (func, wait) => {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };

        // Apply debouncing to form inputs
        document.addEventListener('input', debounce((event) => {
            if (event.target.matches('input[type="text"], input[type="email"], input[type="tel"]')) {
                // Handle debounced input validation
                this.validateInput(event.target);
            }
        }, 300), { passive: true });
    }

    /**
     * Validate input with performance optimization
     */
    validateInput(input) {
        // Use requestIdleCallback if available
        if ('requestIdleCallback' in window) {
            requestIdleCallback(() => {
                this.performValidation(input);
            });
        } else {
            // Fallback to setTimeout
            setTimeout(() => {
                this.performValidation(input);
            }, 0);
        }
    }

    /**
     * Perform actual validation
     */
    performValidation(input) {
        // Lightweight validation logic here
        const value = input.value.trim();
        
        if (input.type === 'tel' && value) {
            const isValid = /^[0-9]{10,11}$/.test(value);
            input.setAttribute('aria-invalid', !isValid);
        }
    }

    /**
     * Setup resource hints
     */
    setupResourceHints() {
        // Add preconnect for external resources
        const preconnectUrls = [
            'https://fonts.googleapis.com',
            'https://cdn.jsdelivr.net'
        ];

        preconnectUrls.forEach(url => {
            const link = document.createElement('link');
            link.rel = 'preconnect';
            link.href = url;
            link.crossOrigin = 'anonymous';
            document.head.appendChild(link);
        });
    }

    /**
     * Log performance metric
     */
    logMetric(name, value) {
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.log(`Performance: ${name} = ${Math.round(value)}ms`);
        }

        // Send to analytics in production
        if (window.gtag) {
            window.gtag('event', 'timing_complete', {
                name: name,
                value: Math.round(value)
            });
        }
    }

    /**
     * Get performance report
     */
    getPerformanceReport() {
        return {
            ...this.metrics,
            currentTime: performance.now(),
            memory: performance.memory ? {
                used: performance.memory.usedJSHeapSize,
                total: performance.memory.totalJSHeapSize,
                limit: performance.memory.jsHeapSizeLimit
            } : null
        };
    }
}

// Initialize performance optimizer
document.addEventListener('DOMContentLoaded', () => {
    window.performanceOptimizer = new PerformanceOptimizer();
});

// Expose performance report for debugging
window.getAuthModalPerformance = () => {
    return window.performanceOptimizer ? 
           window.performanceOptimizer.getPerformanceReport() : 
           null;
};

console.log('Performance Optimizer loaded');
