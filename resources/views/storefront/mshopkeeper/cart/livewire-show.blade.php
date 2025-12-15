@extends('layouts.shop')
@section('title', 'Giỏ hàng - Vũ Phúc Baking')

@section('content')
    @livewire(\App\Livewire\MShopKeeper\CartPage::class)

@endsection

@push('scripts')
<script>
// Listen for Livewire notifications
document.addEventListener('livewire:init', () => {
    Livewire.on('showNotification', (event) => {
        const { message, type } = event[0];
        showNotification(message, type);
    });
});

// Listen for authentication events to refresh cart
document.addEventListener('DOMContentLoaded', function() {
    // Listen for successful login from modal
    window.addEventListener('customer-logged-in', function(event) {
        // Refresh the cart page
        if (window.Livewire) {
            window.Livewire.dispatch('customer-logged-in');
        }
    });

    // Listen for logout
    window.addEventListener('customer-logged-out', function(event) {
        // Refresh the cart page
        if (window.Livewire) {
            window.Livewire.dispatch('customer-logged-out');
        }
    });
});

// Auto-open modal if redirected with show_login_popup
@if(session('show_login_popup'))
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            openAuthModal('login', '{{ url()->current() }}');
        }, 500);
    });
@endif

// Modal function is already available globally from basic-modal.blade.php

// Show notification function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;
    
    // Set colors based on type
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    notification.className += ` ${colors[type] || colors.info}`;
    
    // Set content
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                ${type === 'success' ? '<i class="fas fa-check-circle"></i>' : 
                  type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' :
                  type === 'warning' ? '<i class="fas fa-exclamation-triangle"></i>' :
                  '<i class="fas fa-info-circle"></i>'}
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}
</script>
@endpush
