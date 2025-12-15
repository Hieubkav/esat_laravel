/**
 * Authentication State Manager
 * Global state management for authentication across Alpine.js components
 */

class AuthStateManager {
    constructor() {
        this.state = {
            isAuthenticated: false,
            user: null,
            loading: false,
            error: null
        };
        
        this.listeners = [];
        this.init();
    }

    /**
     * Initialize state manager
     */
    init() {
        this.loadInitialState();
        this.setupEventListeners();
        this.exposeToWindow();
    }

    /**
     * Load initial authentication state
     */
    loadInitialState() {
        // Check if user data is available in DOM
        const userDataElement = document.querySelector('[data-auth-user]');
        if (userDataElement) {
            try {
                const userData = JSON.parse(userDataElement.dataset.authUser);
                this.setState({
                    isAuthenticated: true,
                    user: userData
                });
            } catch (error) {
                console.warn('Failed to parse user data:', error);
            }
        }

        // Check authentication status via meta tag or other indicators
        const authStatus = document.querySelector('meta[name="auth-status"]');
        if (authStatus) {
            this.setState({
                isAuthenticated: authStatus.content === 'authenticated'
            });
        }
    }

    /**
     * Setup global event listeners
     */
    setupEventListeners() {
        // Listen for custom authentication events
        window.addEventListener('customer-logged-in', (event) => {
            this.handleLogin(event.detail);
        });

        window.addEventListener('customer-registered', (event) => {
            this.handleLogin(event.detail);
        });

        window.addEventListener('customer-logged-out', () => {
            this.handleLogout();
        });

        // Listen for Livewire events
        document.addEventListener('livewire:init', () => {
            if (window.Livewire) {
                window.Livewire.on('customer-logged-in', (data) => {
                    this.handleLogin(data);
                });

                window.Livewire.on('customer-registered', (data) => {
                    this.handleLogin(data);
                });

                window.Livewire.on('customer-logged-out', () => {
                    this.handleLogout();
                });
            }
        });
    }

    /**
     * Handle successful login/registration
     */
    handleLogin(data) {
        this.setState({
            isAuthenticated: true,
            user: data.user || data,
            loading: false,
            error: null
        });

        // Emit global event
        this.emit('auth:login', this.state);
    }

    /**
     * Handle logout
     */
    handleLogout() {
        this.setState({
            isAuthenticated: false,
            user: null,
            loading: false,
            error: null
        });

        // Emit global event
        this.emit('auth:logout', this.state);
    }

    /**
     * Set loading state
     */
    setLoading(loading) {
        this.setState({ loading });
    }

    /**
     * Set error state
     */
    setError(error) {
        this.setState({ error });
    }

    /**
     * Update state and notify listeners
     */
    setState(newState) {
        const oldState = { ...this.state };
        this.state = { ...this.state, ...newState };
        
        // Notify all listeners
        this.listeners.forEach(listener => {
            try {
                listener(this.state, oldState);
            } catch (error) {
                console.error('Error in auth state listener:', error);
            }
        });
    }

    /**
     * Get current state
     */
    getState() {
        return { ...this.state };
    }

    /**
     * Subscribe to state changes
     */
    subscribe(listener) {
        this.listeners.push(listener);
        
        // Return unsubscribe function
        return () => {
            const index = this.listeners.indexOf(listener);
            if (index > -1) {
                this.listeners.splice(index, 1);
            }
        };
    }

    /**
     * Emit custom event
     */
    emit(eventName, data) {
        const event = new CustomEvent(eventName, {
            detail: data,
            bubbles: true
        });
        window.dispatchEvent(event);
    }

    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        return this.state.isAuthenticated;
    }

    /**
     * Get current user
     */
    getUser() {
        return this.state.user;
    }

    /**
     * Check if user has specific role or permission
     */
    hasRole(role) {
        return this.state.user && this.state.user.roles && this.state.user.roles.includes(role);
    }

    /**
     * Refresh authentication state from server
     */
    async refreshState() {
        this.setLoading(true);
        
        try {
            const response = await fetch('/api/auth/status', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.setState({
                    isAuthenticated: data.authenticated,
                    user: data.user,
                    loading: false
                });
            } else {
                this.handleLogout();
            }
        } catch (error) {
            console.error('Failed to refresh auth state:', error);
            this.setState({
                loading: false,
                error: 'Failed to refresh authentication state'
            });
        }
    }

    /**
     * Expose methods to window for Alpine.js access
     */
    exposeToWindow() {
        window.authState = {
            // State getters
            get isAuthenticated() { return window.authStateManager.isAuthenticated(); },
            get user() { return window.authStateManager.getUser(); },
            get loading() { return window.authStateManager.state.loading; },
            get error() { return window.authStateManager.state.error; },
            
            // Methods
            subscribe: (listener) => window.authStateManager.subscribe(listener),
            refresh: () => window.authStateManager.refreshState(),
            hasRole: (role) => window.authStateManager.hasRole(role),
            
            // For Alpine.js reactive data
            reactive() {
                return {
                    isAuthenticated: window.authStateManager.isAuthenticated(),
                    user: window.authStateManager.getUser(),
                    loading: window.authStateManager.state.loading,
                    error: window.authStateManager.state.error,
                    
                    // Subscribe to changes
                    init() {
                        window.authStateManager.subscribe((newState) => {
                            this.isAuthenticated = newState.isAuthenticated;
                            this.user = newState.user;
                            this.loading = newState.loading;
                            this.error = newState.error;
                        });
                    }
                };
            }
        };
    }
}

// Create global instance
window.authStateManager = new AuthStateManager();

// Alpine.js global data
document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        // Register global auth state
        window.Alpine.store('auth', window.authState.reactive());
    }
});

console.log('Auth State Manager initialized');
