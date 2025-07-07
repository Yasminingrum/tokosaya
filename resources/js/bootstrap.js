// Bootstrap JavaScript functionality
import axios from 'axios';

// Make axios available globally
window.axios = axios;

// Set default headers
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF Token setup
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Request/Response interceptors for loading states
window.axios.interceptors.request.use(function (config) {
    // Show loading indicator if needed
    document.body.classList.add('loading');
    return config;
}, function (error) {
    document.body.classList.remove('loading');
    return Promise.reject(error);
});

window.axios.interceptors.response.use(function (response) {
    // Hide loading indicator
    document.body.classList.remove('loading');
    return response;
}, function (error) {
    document.body.classList.remove('loading');

    // Handle common errors
    if (error.response) {
        switch (error.response.status) {
            case 401:
                // Unauthorized - redirect to login
                window.location.href = '/login';
                break;
            case 403:
                // Forbidden
                console.error('Access forbidden');
                break;
            case 419:
                // CSRF token mismatch
                console.error('CSRF token mismatch. Please refresh the page.');
                break;
            case 500:
                // Server error
                console.error('Server error occurred');
                break;
        }
    }

    return Promise.reject(error);
});

// Helper functions
window.showToast = function(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;

    document.body.appendChild(toast);

    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
};

// Format currency helper
window.formatCurrency = function(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

// Debounce helper
window.debounce = function(func, wait, immediate) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
};
