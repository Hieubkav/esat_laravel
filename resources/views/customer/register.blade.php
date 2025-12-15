@extends('layouts.shop')

@section('content')
<div class="min-h-screen bg-white flex items-center justify-center py-4 px-3">
    <div class="w-full max-w-xs sm:max-w-sm">
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-gray-900">Đăng ký</h1>
        </div>

        <!-- Form -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 sm:p-5">
            @if ($errors->any())
                <div class="mb-3 p-2.5 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('customer.register') }}" class="space-y-2.5" id="register-form">
                @csrf

                <div class="relative">
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Họ và tên *">
                    <div class="error-message text-xs text-red-500 mt-1 hidden" id="name-error"></div>
                </div>

                <div class="relative">
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Email">
                    <div class="text-xs text-blue-600 mt-1">
                        💡 Khuyến khích nhập email để lấy lại mật khẩu khi quên
                    </div>
                    <div class="error-message text-xs text-red-500 mt-1 hidden" id="email-error"></div>
                </div>

                <div class="relative">
                    <input type="tel"
                           name="phone"
                           id="phone"
                           value="{{ old('phone') }}"
                           required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Số điện thoại *">
                    <div class="error-message text-xs text-red-500 mt-1 hidden" id="phone-error"></div>
                </div>

                <div class="relative">
                    <select name="gender"
                            id="gender"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                        <option value="">Chọn giới tính</option>
                        <option value="0" {{ old('gender') == '0' ? 'selected' : '' }}>Nam</option>
                        <option value="1" {{ old('gender') == '1' ? 'selected' : '' }}>Nữ</option>
                    </select>
                    <div class="error-message text-xs text-red-500 mt-1 hidden" id="gender-error"></div>
                </div>

                <div class="relative">
                    <textarea name="address"
                              id="address"
                              rows="2"
                              class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all resize-none"
                              placeholder="Địa chỉ">{{ old('address') }}</textarea>
                    <div class="error-message text-xs text-red-500 mt-1 hidden" id="address-error"></div>
                </div>

                <div class="relative">
                    <input type="text"
                           name="identify_number"
                           id="identify_number"
                           value="{{ old('identify_number') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Số CMND/CCCD">
                    <div class="error-message text-xs text-red-500 mt-1 hidden" id="identify_number-error"></div>
                </div>

                <div class="relative">
                    <input type="password"
                           name="password"
                           id="password"
                           required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Mật khẩu *">
                    <div class="error-message text-xs text-red-500 mt-1 hidden" id="password-error"></div>
                </div>

                <div class="relative">
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Xác nhận mật khẩu *">
                    <div class="error-message text-xs text-red-500 mt-1 hidden" id="password_confirmation-error"></div>
                </div>

                <div class="text-xs text-gray-500 py-1" id="contact-info-note">
                    Cần ít nhất email hoặc số điện thoại
                </div>
                <div class="error-message text-xs text-red-500 hidden" id="contact-error">
                    Vui lòng nhập ít nhất email hoặc số điện thoại
                </div>

                <button type="submit"
                        id="submit-btn"
                        class="w-full bg-red-600 text-white py-2 text-sm font-medium rounded-lg hover:bg-red-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <span id="submit-text">Đăng ký</span>
                    <span id="submit-loading" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-1"></i>Đang xử lý...
                    </span>
                </button>
            </form>

        </div>

        <!-- Links -->
        <div class="mt-3 text-center space-y-1.5">
            <div class="text-sm text-gray-600">
                Đã có tài khoản?
                <a href="{{ route('customer.login') }}" class="text-red-600 hover:text-red-700 font-medium">Đăng nhập</a>
            </div>
            <a href="{{ route('storeFront') }}" class="block text-xs text-gray-500 hover:text-gray-700">← Trang chủ</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');

    // Validation rules
    const validationRules = {
        name: {
            required: true,
            minLength: 2,
            maxLength: 255,
            messages: {
                required: 'Vui lòng nhập họ và tên',
                minLength: 'Họ và tên phải có ít nhất 2 ký tự',
                maxLength: 'Họ và tên không được vượt quá 255 ký tự'
            }
        },
        email: {
            email: true,
            messages: {
                email: 'Email không đúng định dạng'
            }
        },
        phone: {
            required: true,
            pattern: /^[0-9]{8,12}$/,
            messages: {
                required: 'Vui lòng nhập số điện thoại',
                pattern: 'Số điện thoại phải có 8-12 chữ số'
            }
        },
        password: {
            required: true,
            minLength: 6,
            messages: {
                required: 'Vui lòng nhập mật khẩu',
                minLength: 'Mật khẩu phải có ít nhất 6 ký tự'
            }
        },
        password_confirmation: {
            required: true,
            match: 'password',
            messages: {
                required: 'Vui lòng xác nhận mật khẩu',
                match: 'Xác nhận mật khẩu không khớp'
            }
        }
    };

    // Validation functions
    function validateField(fieldName, value) {
        const rules = validationRules[fieldName];
        if (!rules) return { valid: true };

        const errors = [];

        // Required validation
        if (rules.required && (!value || value.trim() === '')) {
            errors.push(rules.messages.required);
        }

        // Skip other validations if field is empty and not required
        if (!value || value.trim() === '') {
            return { valid: errors.length === 0, errors };
        }

        // Min length validation
        if (rules.minLength && value.length < rules.minLength) {
            errors.push(rules.messages.minLength);
        }

        // Max length validation
        if (rules.maxLength && value.length > rules.maxLength) {
            errors.push(rules.messages.maxLength);
        }

        // Email validation
        if (rules.email) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(value)) {
                errors.push(rules.messages.email);
            }
        }

        // Pattern validation
        if (rules.pattern && !rules.pattern.test(value)) {
            errors.push(rules.messages.pattern);
        }

        // Match validation (for password confirmation)
        if (rules.match) {
            const matchField = document.getElementById(rules.match);
            if (matchField && value !== matchField.value) {
                errors.push(rules.messages.match);
            }
        }

        return { valid: errors.length === 0, errors };
    }

    function showFieldError(fieldName, errors) {
        const field = document.getElementById(fieldName);
        const errorDiv = document.getElementById(fieldName + '-error');

        if (errors.length > 0) {
            field.classList.remove('border-gray-200', 'focus:border-red-500');
            field.classList.add('border-red-300', 'focus:border-red-500');
            errorDiv.textContent = errors[0];
            errorDiv.classList.remove('hidden');
        } else {
            field.classList.remove('border-red-300');
            field.classList.add('border-gray-200', 'focus:border-red-500');
            errorDiv.classList.add('hidden');
        }
    }

    function validateContactInfo() {
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const contactError = document.getElementById('contact-error');
        const contactNote = document.getElementById('contact-info-note');

        if (!email && !phone) {
            contactError.classList.remove('hidden');
            contactNote.classList.add('hidden');
            return false;
        } else {
            contactError.classList.add('hidden');
            contactNote.classList.remove('hidden');
            return true;
        }
    }

    // Real-time validation
    Object.keys(validationRules).forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            // Validate on blur
            field.addEventListener('blur', function() {
                const result = validateField(fieldName, this.value);
                showFieldError(fieldName, result.errors);

                // Special case for contact info
                if (fieldName === 'email' || fieldName === 'phone') {
                    validateContactInfo();
                }
            });

            // Validate on input (debounced)
            let timeout;
            field.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const result = validateField(fieldName, this.value);
                    showFieldError(fieldName, result.errors);

                    // Special case for contact info
                    if (fieldName === 'email' || fieldName === 'phone') {
                        validateContactInfo();
                    }
                }, 500);
            });
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        let isValid = true;

        // Validate all fields
        Object.keys(validationRules).forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                const result = validateField(fieldName, field.value);
                showFieldError(fieldName, result.errors);
                if (!result.valid) isValid = false;
            }
        });

        // Validate contact info
        if (!validateContactInfo()) {
            isValid = false;
        }

        if (isValid) {
            // Show loading state
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            submitLoading.classList.remove('hidden');

            // Submit form
            this.submit();
        }
    });
});
</script>
@endpush

@endsection
