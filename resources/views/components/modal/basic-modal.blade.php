<div id="authModal" class="fixed inset-0 z-50 hidden" x-cloak
     x-data="{
         activeTab: 'login',
         step1Phone: '',
         close() {
             console.log('🔍 Alpine close called');
             const modal = document.getElementById('authModal');
             if (modal) modal.classList.add('hidden');
         },
         switchTab(tab) {
             console.log('🔍 Alpine switching to:', tab);
             this.activeTab = tab;

             // Update modal title
             const title = document.getElementById('modalTitle');
             if (title) {
                 title.textContent = tab === 'login' ? 'Đăng nhập' :
                                   tab === 'register' ? 'Đăng ký' : 'Tạo mật khẩu';
             }

             // Show/hide forms
             const forms = ['loginForm', 'registerForm', 'createPasswordForm'];
             forms.forEach(formId => {
                 const form = document.getElementById(formId);
                 if (form) {
                     if ((formId === 'loginForm' && tab === 'login') ||
                         (formId === 'registerForm' && tab === 'register') ||
                         (formId === 'createPasswordForm' && tab === 'createPassword')) {
                         form.classList.remove('hidden');
                     } else {
                         form.classList.add('hidden');
                     }
                 }
             });
         }
     }"
     x-on:keydown.escape.window="close()"
     x-on:auth-modal-open.window="
         activeTab = $event.detail?.tab || 'login';
         $el.classList.remove('hidden');
         console.log('🔍 Alpine modal opened, tab:', activeTab);
     ">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75" x-on:click="close()" id="modalBackdrop"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-8" x-on:click.stop>

            <div class="absolute right-4 top-4">
                <button x-on:click="close()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="text-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Đăng nhập</h3>
            </div>

            <div class="flex mb-6 bg-gray-100 rounded-lg p-1">
                <button x-on:click="switchTab('login')" id="loginTab"
                        x-bind:class="activeTab === 'login' ? 'flex-1 py-2 px-3 text-sm font-medium rounded-md bg-white text-red-600 shadow-sm' : 'flex-1 py-2 px-3 text-sm font-medium rounded-md text-gray-500'">
                    Đăng nhập
                </button>
                <button x-on:click="switchTab('register')" id="registerTab"
                        x-bind:class="activeTab === 'register' ? 'flex-1 py-2 px-3 text-sm font-medium rounded-md bg-white text-red-600 shadow-sm' : 'flex-1 py-2 px-3 text-sm font-medium rounded-md text-gray-500'">
                    Đăng ký
                </button>
                <button x-on:click="switchTab('createPassword')" id="createPasswordTab"
                        class="flex-1 py-2 px-3 text-sm font-medium rounded-md text-gray-500 hidden">
                    Tạo mật khẩu
                </button>
            </div>

            <div id="loginForm" x-show="activeTab === 'login'">
                <form action="{{ route('modal.login') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="redirect_to" id="loginRedirectTo" value="{{ url()->current() }}">

                    <!-- Display login errors -->
                    @if (session('modal_login_error') && $errors->has('login'))
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="text-sm text-red-600">
                                {{ $errors->first('login') }}
                            </div>
                        </div>
                    @endif

                    <div>
                        <div class="relative">
                            <input type="tel" name="login" id="login-phone" required value="{{ old('login') }}"
                                   class="w-full px-3 py-2 pr-10 border rounded-lg focus:outline-none focus:border-red-500 @error('login') border-red-500 @else border-gray-200 @enderror"
                                   placeholder="Số điện thoại (8-12 chữ số)">
                            <button type="button" id="clear-phone-btn"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500 focus:outline-none hidden">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                        @error('login')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <div class="relative">
                            <input type="password" name="password" id="login-password" required
                                   class="w-full px-3 py-2 pr-20 border rounded-lg focus:outline-none focus:border-red-500 @error('password') border-red-500 @else border-gray-200 @enderror"
                                   placeholder="Mật khẩu">
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center space-x-2">
                                <button type="button" id="clear-password-btn"
                                        class="text-gray-400 hover:text-red-500 focus:outline-none hidden">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <button type="button" id="toggle-password-btn"
                                        class="text-gray-400 hover:text-blue-500 focus:outline-none">
                                    <svg class="w-5 h-5" id="eye-closed" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88"></path>
                                    </svg>
                                    <svg class="w-5 h-5 hidden" id="eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit"
                            class="w-full bg-red-600 text-white py-2 font-medium rounded-lg hover:bg-red-700">
                        Đăng nhập
                    </button>

                    <!-- Forgot Password Link -->
                    <div class="text-center">
                        <a href="{{ route('customer.password.request') }}" class="text-sm text-gray-600 hover:text-gray-800">Quên mật khẩu?</a>
                    </div>
                </form>
            </div>

            <div id="registerForm" x-show="activeTab === 'register'">
                <!-- Step Progress Indicator -->
                <div class="mb-8">
                    <div class="flex items-center justify-center space-x-6">
                        <div class="flex items-center">
                            <div id="step1-indicator" class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center text-base font-medium">1</div>
                            <span id="step1-label" class="ml-3 text-base font-medium text-red-600">Số điện thoại</span>
                        </div>
                        <div class="w-12 h-0.5 bg-gray-200" id="step-connector"></div>
                        <div class="flex items-center">
                            <div id="step2-indicator" class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-base font-medium">2</div>
                            <span id="step2-label" class="ml-3 text-base text-gray-500">Thông tin</span>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Phone Input -->
                <div id="step1-container" class="step-container">
                    <div class="text-center mb-8">
                        <h4 class="text-xl font-semibold text-gray-900 mb-3">Nhập số điện thoại</h4>
                        <p class="text-base text-gray-600">Chúng tôi sẽ kiểm tra xem bạn đã có tài khoản chưa</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <input type="tel" id="step1-phone" required x-model="step1Phone" x-on:input="step1Phone = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = step1Phone"
                                   class="w-full px-5 py-4 border rounded-lg focus:outline-none focus:border-red-500 border-gray-200 text-center text-lg"
                                   placeholder="Nhập số điện thoại của bạn">
                            <div id="step1-phone-message" class="text-sm mt-3 hidden"></div>
                        </div>

                        <button type="button" id="step1-continue" x-bind:disabled="(step1Phone || '').replace(/[^0-9]/g, '').length < 10"
                                class="w-full bg-red-600 text-white py-4 font-semibold text-lg rounded-lg hover:bg-red-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                                disabled>
                            <span class="continue-text">Tiếp tục</span>
                            <span class="continue-loading hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Đang kiểm tra...
                            </span>
                        </button>

                        <!-- HAS_ACCOUNT Case -->
                        <div id="has-account-message" class="bg-yellow-50 border border-yellow-200 rounded-lg p-5 hidden">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-base font-medium text-yellow-800">Số điện thoại đã có tài khoản</p>
                                    <p class="text-sm text-yellow-600 mt-1">Vui lòng chuyển sang tab Đăng nhập để truy cập tài khoản của bạn.</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="button" id="switch-to-login" class="text-sm bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg hover:bg-yellow-200 font-medium transition-colors">
                                    Chuyển sang Đăng nhập
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Registration Form -->
                <div id="step2-container" class="step-container hidden">
                    <form action="{{ route('modal.register') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="redirect_to" id="registerRedirectTo" value="{{ url()->current() }}">

                        <!-- Display validation errors -->
                        @if (session('modal_register_error') && $errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <div class="text-sm text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Step 2 Header -->
                        <div id="step2-header" class="text-center mb-6">
                            <h4 id="step2-title" class="text-xl font-semibold text-gray-900 mb-3">Hoàn tất đăng ký</h4>
                            <p id="step2-subtitle" class="text-base text-gray-600">Điền thông tin để tạo tài khoản</p>
                        </div>

                        <!-- Auto-fill notification -->
                        <div id="autofill-notification" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 hidden">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Tài khoản đã tồn tại trong hệ thống</p>
                                    <p class="text-xs text-blue-600 mt-1">Thông tin được bảo vệ và không thể chỉnh sửa. Chỉ cần tạo mật khẩu để hoàn tất.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Back button -->
                        <div class="flex items-center mb-6">
                            <button type="button" id="step2-back" class="text-base text-gray-500 hover:text-gray-700 flex items-center font-medium">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Quay lại
                            </button>
                        </div>

                        <!-- Hidden phone field for form submission -->
                        <input type="hidden" name="phone" id="final-phone" value="{{ old('phone', '') }}">

                        <div class="space-y-5">
                            <!-- Thông tin cá nhân -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-semibold text-gray-700 border-b border-gray-200 pb-2">Thông tin cá nhân</h4>

                                <!-- Row 1: Name + Email -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="register-name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Họ và tên <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="name" id="register-name" required value="{{ old('name', '') }}"
                                               class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-red-500 @error('name') border-red-500 @else border-gray-200 @enderror text-base"
                                               placeholder="Nhập họ và tên đầy đủ">
                                        @error('name')
                                            <div class="text-sm text-red-500 mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="register-email" class="block text-sm font-medium text-gray-700 mb-2">
                                            Email
                                        </label>
                                        <input type="email" name="email" id="register-email" value="{{ old('email', '') }}"
                                               class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-red-500 @error('email') border-red-500 @else border-gray-200 @enderror text-base"
                                               placeholder="example@email.com">
                                        @error('email')
                                            <div class="text-sm text-red-500 mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Row 2: Gender + ID Number -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="register-gender" class="block text-sm font-medium text-gray-700 mb-2">
                                            Giới tính
                                        </label>
                                        <select name="gender" id="register-gender"
                                                class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-red-500 @error('gender') border-red-500 @else border-gray-200 @enderror text-base">
                                            <option value="" {{ old('gender', '') == '' ? 'selected' : '' }}>Chọn giới tính</option>
                                            <option value="0" {{ old('gender') == '0' ? 'selected' : '' }}>Nam</option>
                                            <option value="1" {{ old('gender') == '1' ? 'selected' : '' }}>Nữ</option>
                                        </select>
                                        @error('gender')
                                            <div class="text-sm text-red-500 mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="register-identify-number" class="block text-sm font-medium text-gray-700 mb-2">
                                            Số CMND/CCCD
                                        </label>
                                        <input type="text" name="identify_number" id="register-identify-number" value="{{ old('identify_number', '') }}"
                                               class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-red-500 @error('identify_number') border-red-500 @else border-gray-200 @enderror text-base"
                                               placeholder="Nhập số CMND hoặc CCCD">
                                        @error('identify_number')
                                            <div class="text-sm text-red-500 mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Row 3: Address (full width) -->
                                <div>
                                    <label for="register-address" class="block text-sm font-medium text-gray-700 mb-2">
                                        Địa chỉ
                                    </label>
                                    <input type="text" name="address" id="register-address" value="{{ old('address', '') }}"
                                           class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-red-500 @error('address') border-red-500 @else border-gray-200 @enderror text-base"
                                           placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố">
                                    @error('address')
                                        <div class="text-sm text-red-500 mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Bảo mật -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-semibold text-gray-700 border-b border-gray-200 pb-2">Bảo mật tài khoản</h4>

                                <!-- Row 4: Password + Confirm Password -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="register-password" class="block text-sm font-medium text-gray-700 mb-2">
                                            Mật khẩu <span class="text-red-500">*</span>
                                        </label>
                                        <input type="password" name="password" id="register-password" required
                                               class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-red-500 @error('password') border-red-500 @else border-gray-200 @enderror text-base"
                                               placeholder="Tối thiểu 8 ký tự">
                                        @error('password')
                                            <div class="text-sm text-red-500 mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="register-password-confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                            Xác nhận mật khẩu <span class="text-red-500">*</span>
                                        </label>
                                        <input type="password" name="password_confirmation" id="register-password-confirmation" required
                                               class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-red-500 @error('password_confirmation') border-red-500 @else border-gray-200 @enderror text-base"
                                               placeholder="Nhập lại mật khẩu">
                                        @error('password_confirmation')
                                            <div class="text-sm text-red-500 mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full bg-red-600 text-white py-3 px-6 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 font-medium text-lg">
                                Đăng ký tài khoản
                            </button>

                            <p class="text-sm text-gray-500 text-center">
                                <span class="text-red-500">*</span> Trường bắt buộc phải điền
                            </p>
                        </div>
                </form>
            </div>

            <!-- Create Password Form -->
            <div id="createPasswordForm" x-show="activeTab === 'createPassword'">
                <!-- Customer Info Display -->
                <div id="customerInfoDisplay" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 hidden">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800" id="customerDisplayName">Khách hàng</h3>
                            <div class="text-sm text-blue-600">
                                <p id="customerDisplayPhone">SĐT: </p>
                                <p id="customerDisplayEmail" class="hidden">Email: </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verification Step -->
                <div id="verificationStep">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-3">Để bảo mật tài khoản, vui lòng xác thực thông tin của bạn:</p>
                    </div>

                    <form id="verificationForm" class="space-y-4">
                        <div>
                            <input type="text" id="verifyName" required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-red-500"
                                   placeholder="Họ và tên đầy đủ *">
                            <div class="text-xs text-red-500 mt-1 hidden" id="verifyNameError"></div>
                        </div>

                        <div>
                            <input type="email" id="verifyEmail"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-red-500"
                                   placeholder="Email (nếu có)">
                            <div class="text-xs text-red-500 mt-1 hidden" id="verifyEmailError"></div>
                        </div>

                        <button type="submit" id="verifyButton"
                                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <span class="verify-text">Xác thực thông tin</span>
                            <span class="verify-loading hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Đang xác thực...
                            </span>
                        </button>
                    </form>
                </div>

                <!-- Password Creation Step -->
                <div id="passwordCreationStep" class="hidden">
                    <div class="mb-4">
                        <div class="flex items-center text-green-600 mb-2">
                            <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Xác thực thành công!</span>
                        </div>
                        <p class="text-sm text-gray-600">Bây giờ hãy tạo mật khẩu cho tài khoản của bạn:</p>
                    </div>

                    <form id="passwordCreationForm" action="/modal/tao-mat-khau" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <input type="password" name="password" id="newPassword" required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-red-500"
                                   placeholder="Mật khẩu mới (tối thiểu 6 ký tự) *">
                            <div class="text-xs text-red-500 mt-1 hidden" id="newPasswordError"></div>
                        </div>

                        <div>
                            <input type="password" name="password_confirmation" id="confirmPassword" required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-red-500"
                                   placeholder="Xác nhận mật khẩu *">
                            <div class="text-xs text-red-500 mt-1 hidden" id="confirmPasswordError"></div>
                        </div>

                        <button type="submit" id="createPasswordButton"
                                class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <span class="create-text">Tạo mật khẩu và đăng nhập</span>
                            <span class="create-loading hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Đang tạo mật khẩu...
                            </span>
                        </button>
                    </form>
                </div>

                <!-- Back to Login -->
                <div class="mt-4 text-center">
                    <button onclick="switchToLogin()" class="text-sm text-gray-600 hover:text-gray-800">
                        ← Quay lại đăng nhập
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Minimalist styling for disabled and readonly fields */
input:disabled, select:disabled, input:read-only {
    cursor: not-allowed;
    opacity: 0.8;
}

input:disabled:focus, select:disabled:focus, input:read-only:focus {
    outline: none;
    box-shadow: none;
    border-color: #d1d5db !important;
}

/* Clean disabled/readonly state without heavy visual indicators */
.form-disabled {
    background-color: #f9fafb !important;
    border-color: #e5e7eb !important;
    color: #374151 !important;
}

/* Readonly fields styling */
input[readonly] {
    background-color: #f9fafb !important;
    border-color: #e5e7eb !important;
    color: #374151 !important;
    cursor: not-allowed !important;
}

input[readonly]:focus {
    outline: none !important;
    box-shadow: none !important;
    border-color: #e5e7eb !important;
}
</style>

<script>
function openAuthModal(tab = 'login', redirectTo = null) {
    // Dispatch Alpine event only; Alpine will show modal and set redirect_to
    window.dispatchEvent(new CustomEvent('auth-modal-open', {
        detail: { tab, redirectTo }
    }));
}

function updateRedirectFields(url) {
    // Update login form redirect field
    const loginRedirectField = document.getElementById('loginRedirectTo');
    if (loginRedirectField) {
        loginRedirectField.value = url;
    }

    // Update register form redirect field
    const registerRedirectField = document.getElementById('registerRedirectTo');
    if (registerRedirectField) {
        registerRedirectField.value = url;
    }
}

// Thin wrappers: delegate to Alpine via events
function closeAuthModal() {
    window.dispatchEvent(new CustomEvent('auth-modal-close'));
}
function switchToLogin() {
    window.dispatchEvent(new CustomEvent('auth-modal-switch', { detail: { tab: 'login' } }));
}
function switchToRegister() {
    window.dispatchEvent(new CustomEvent('auth-modal-switch', { detail: { tab: 'register' } }));
}
function switchToCreatePassword() {
    window.dispatchEvent(new CustomEvent('auth-modal-switch', { detail: { tab: 'createPassword' } }));
}

// Enhanced authentication flow variables
let currentCustomerData = null;
let currentPhone = null;



// Function to setup clear button functionality
function setupInputClearButtons() {
    const phoneInput = document.getElementById('login-phone');
    const passwordInput = document.getElementById('login-password');
    const clearPhoneBtn = document.getElementById('clear-phone-btn');
    const clearPasswordBtn = document.getElementById('clear-password-btn');

    // Phone input clear button
    if (phoneInput && clearPhoneBtn) {
        phoneInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                clearPhoneBtn.classList.remove('hidden');
            } else {
                clearPhoneBtn.classList.add('hidden');
            }
        });

        clearPhoneBtn.addEventListener('click', function() {
            phoneInput.value = '';
            phoneInput.focus();
            clearPhoneBtn.classList.add('hidden');
        });

        // Initial check
        if (phoneInput.value.length > 0) {
            clearPhoneBtn.classList.remove('hidden');
        }
    }

    // Password input clear button
    if (passwordInput && clearPasswordBtn) {
        passwordInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                clearPasswordBtn.classList.remove('hidden');
            } else {
                clearPasswordBtn.classList.add('hidden');
            }
        });

        clearPasswordBtn.addEventListener('click', function() {
            passwordInput.value = '';
            passwordInput.focus();
            clearPasswordBtn.classList.add('hidden');
        });

        // Initial check
        if (passwordInput.value.length > 0) {
            clearPasswordBtn.classList.remove('hidden');
        }
    }
}

// Function to setup password toggle
function setupPasswordToggle() {
    const passwordInput = document.getElementById('login-password');
    const toggleBtn = document.getElementById('toggle-password-btn');
    const eyeClosed = document.getElementById('eye-closed');
    const eyeOpen = document.getElementById('eye-open');

    if (passwordInput && toggleBtn && eyeClosed && eyeOpen) {
        toggleBtn.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeClosed.classList.add('hidden');
                eyeOpen.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeClosed.classList.remove('hidden');
                eyeOpen.classList.add('hidden');
            }
        });
    }
}

// Enhanced login form handler
document.addEventListener('DOMContentLoaded', function() {
    // Handle login form submission
    const loginForm = document.querySelector('#loginForm form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleEnhancedLogin(this);
        });
    }

    // Handle verification form
    const verificationForm = document.getElementById('verificationForm');
    if (verificationForm) {
        verificationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleVerification();
        });
    }

    // Handle password creation form
    const passwordCreationForm = document.getElementById('passwordCreationForm');
    if (passwordCreationForm) {
        console.log('🔑 Password form found, adding event listener');
        passwordCreationForm.addEventListener('submit', function(e) {
            console.log('🔑 Password form submitted!');
            e.preventDefault();
            handlePasswordCreation(this);
        });
    } else {
        console.log('❌ Password form not found!');
    }

    // Handle registration form submission using event delegation
    document.addEventListener('submit', function(e) {
        if (e.target.matches('#step2-container form')) {
            e.preventDefault();
            handleRegistration(e.target);
        }
    });

    // Setup input event listeners for clear buttons
    setupInputClearButtons();
    setupPasswordToggle();

    // Add backup event listeners for modal controls
    const modal = document.getElementById('authModal');
    if (modal) {
        // Backup backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.id === 'modalBackdrop') {
                console.log('🔍 Backdrop clicked - closing modal');
                closeAuthModal();
            }
        });

        // Backup ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                console.log('🔍 ESC pressed - closing modal');
                closeAuthModal();
            }
        });
    }

    // Auto open modal if there are validation errors from modal
    @if (session('modal_login_error'))
        window.dispatchEvent(new CustomEvent('auth-modal-open', { detail: { tab: 'login' } }));
    @elseif (session('modal_register_error'))
        window.dispatchEvent(new CustomEvent('auth-modal-open', { detail: { tab: 'register' } }));
    @endif
});

// Enhanced login handler
async function handleEnhancedLogin(form) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    // Show loading
    submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Đang đăng nhập...';
    submitButton.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            if (data.action === 'CREATE_PASSWORD') {
                // Switch to create password flow
                currentCustomerData = data.customer_data;
                currentPhone = formData.get('login');
                showCreatePasswordFlow();
            } else if (data.action === 'SUGGEST_REGISTER') {
                // Show register suggestion
                showRegisterSuggestion();
            } else {
                // Normal login success - handle delay_redirect
                const redirectDelay = data.delay_redirect || 0;

                if (redirectDelay > 0) {
                    // Show success message first
                    showSuccessMessage(data.message || 'Đăng nhập thành công!');

                    // Delay redirect để đảm bảo session sync
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, redirectDelay);
                } else {
                    // Immediate redirect
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                }
            }
        } else {
            // Show error
            showLoginError(data.errors?.login || 'Đăng nhập thất bại');
        }
    } catch (error) {
        showLoginError('Có lỗi xảy ra. Vui lòng thử lại.');
    } finally {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }
}

// Show create password flow
function showCreatePasswordFlow() {
    switchToCreatePassword();

    // Display customer info
    if (currentCustomerData) {
        const customerInfo = document.getElementById('customerInfoDisplay');
        const customerName = document.getElementById('customerDisplayName');
        const customerPhone = document.getElementById('customerDisplayPhone');
        const customerEmail = document.getElementById('customerDisplayEmail');

        customerName.textContent = currentCustomerData.Name || currentCustomerData.name || 'Khách hàng';
        customerPhone.textContent = 'SĐT: ' + (currentCustomerData.Tel || currentCustomerData.tel || currentPhone);

        if (currentCustomerData.Email || currentCustomerData.email) {
            customerEmail.textContent = 'Email: ' + (currentCustomerData.Email || currentCustomerData.email);
            customerEmail.classList.remove('hidden');
        }

        customerInfo.classList.remove('hidden');
    }
}

// Handle verification
async function handleVerification() {
    const verifyButton = document.getElementById('verifyButton');
    const verifyText = verifyButton.querySelector('.verify-text');
    const verifyLoading = verifyButton.querySelector('.verify-loading');

    verifyText.classList.add('hidden');
    verifyLoading.classList.remove('hidden');
    verifyButton.disabled = true;

    const formData = {
        phone: currentPhone,
        name: document.getElementById('verifyName').value,
        email: document.getElementById('verifyEmail').value
    };

    try {
        const response = await fetch('/api/customer/verify-identity', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success && data.verified) {
            // Show password creation step
            document.getElementById('verificationStep').classList.add('hidden');
            document.getElementById('passwordCreationStep').classList.remove('hidden');
        } else {
            showVerificationError(data.message || 'Thông tin xác thực không khớp');
        }
    } catch (error) {
        showVerificationError('Có lỗi xảy ra khi xác thực');
    } finally {
        verifyText.classList.remove('hidden');
        verifyLoading.classList.add('hidden');
        verifyButton.disabled = false;
    }
}

// OLD FUNCTION REMOVED - Using the new handlePasswordCreation(form) function below

// Handle registration form submission
async function handleRegistration(form) {
    console.log('🚀 Starting registration...');

    let formData = new FormData(form);

    // Thêm URL hiện tại để redirect về sau khi tạo mật khẩu
    formData.append('current_url', window.location.href);

    // Nếu là existing customer (có auto-fill), chỉ gửi password + phone
    if (currentCustomerData) {
        console.log('🔄 Existing customer mode - sending minimal data');
        const minimalData = new FormData();
        minimalData.append('phone', formData.get('phone'));
        minimalData.append('password', formData.get('password'));
        minimalData.append('password_confirmation', formData.get('password_confirmation'));
        minimalData.append('current_url', formData.get('current_url'));
        minimalData.append('_token', formData.get('_token'));

        // Copy auto-filled data để backend có thể validate nếu cần
        minimalData.append('name', currentCustomerData.name || '');
        minimalData.append('email', currentCustomerData.email || '');
        minimalData.append('address', currentCustomerData.addr || '');
        minimalData.append('identify_number', currentCustomerData.identify_number || '');
        minimalData.append('gender', currentCustomerData.gender || '0');

        formData = minimalData;
    }

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    // Debug: Log form data
    console.log('📝 Form data:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }

    // Show loading với text phù hợp
    const loadingText = currentCustomerData ? 'Đang tạo mật khẩu...' : 'Đang đăng ký...';
    submitButton.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>${loadingText}`;
    submitButton.disabled = true;

    try {
        console.log('📡 Sending request to:', form.action);

        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        console.log('📨 Response status:', response.status);
        console.log('📨 Response headers:', response.headers);

        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            console.error('❌ Response is not JSON:', contentType);
            const textResponse = await response.text();
            console.error('📄 Response text:', textResponse);
            showRegistrationError('Server trả về dữ liệu không đúng định dạng. Vui lòng thử lại.');
            return;
        }

        const data = await response.json();
        console.log('📦 Response data:', data);

        if (data.success) {
            console.log('✅ Registration successful');

            // Check if need to show password form
            if (data.show_password_form) {
                console.log('🔑 Showing password creation form');
                showPasswordCreationForm(data.customer_name, data.message);
                return;
            }

            // Registration successful - close modal and handle redirect
            closeAuthModal();

            // Show success message
            showSuccessMessage(data.message || 'Đăng ký thành công!');

            // Trigger Livewire refresh for auth components
            if (window.Livewire) {
                window.Livewire.dispatch('customer-registered');
                window.Livewire.dispatch('customer-logged-in');
            }

            // Emit global auth events
            window.dispatchEvent(new CustomEvent('customer-registered', {
                detail: { user: data.user }
            }));
            window.dispatchEvent(new CustomEvent('customer-logged-in', {
                detail: { user: data.user }
            }));

            // Handle redirect with delay
            const redirectDelay = data.delay_redirect || 800;
            setTimeout(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            }, redirectDelay);
        } else {
            console.log('❌ Registration failed:', data);

            // Handle validation errors
            if (data.errors) {
                console.log('📋 Validation errors:', data.errors);
                displayRegistrationErrors(data.errors);
            } else {
                const errorMessage = data.message || 'Có lỗi xảy ra khi đăng ký';
                console.log('💥 Error message:', errorMessage);
                showRegistrationError(errorMessage);
            }
        }
    } catch (error) {
        console.error('💥 Registration error:', error);
        showRegistrationError('Có lỗi xảy ra. Vui lòng thử lại. Chi tiết: ' + error.message);
    } finally {
        // Restore button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }
}

// Helper functions
function showLoginError(message) {
    // Implementation for showing login errors
    alert(message); // Temporary - should be replaced with proper UI
}

function showRegisterSuggestion() {
    switchToRegister();
}

function showVerificationError(message) {
    document.getElementById('verifyNameError').textContent = message;
    document.getElementById('verifyNameError').classList.remove('hidden');
}

function showPasswordError(fieldId, message) {
    const errorElement = document.getElementById(fieldId + 'Error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }
}

function showRegistrationError(message) {
    console.log('🚨 Showing registration error:', message);

    // Show error in step 2 form
    let errorDiv = document.getElementById('registration-error');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'registration-error';
        errorDiv.className = 'bg-red-50 border border-red-200 rounded-lg p-3 mb-4';

        const step2Container = document.getElementById('step2-container');
        const form = step2Container.querySelector('form');
        if (form) {
            form.insertBefore(errorDiv, form.firstChild);
        } else {
            console.error('❌ Form not found in step2-container');
            // Fallback: show alert
            alert('Lỗi: ' + message);
            return;
        }
    }

    // Ensure message is a string and not empty
    const displayMessage = typeof message === 'string' && message.trim()
        ? message
        : 'Có lỗi xảy ra. Vui lòng thử lại.';

    errorDiv.innerHTML = `
        <div class="flex items-start">
            <svg class="w-4 h-4 text-red-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-sm text-red-600 flex-1">
                ${displayMessage}
            </div>
        </div>
    `;
    errorDiv.classList.remove('hidden');

    // Scroll to error
    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function displayRegistrationErrors(errors) {
    console.log('📋 Displaying registration errors:', errors);

    // Clear previous errors
    clearRegistrationErrors();

    // Collect all error messages
    const allErrors = [];

    Object.keys(errors).forEach(field => {
        if (Array.isArray(errors[field])) {
            errors[field].forEach(error => {
                allErrors.push(`${getFieldDisplayName(field)}: ${error}`);
            });
        } else if (typeof errors[field] === 'string') {
            allErrors.push(`${getFieldDisplayName(field)}: ${errors[field]}`);
        }
    });

    // Show all errors in one message
    if (allErrors.length > 0) {
        const errorMessage = allErrors.join('<br>');
        showRegistrationError(errorMessage);
    }

    // Also show field-specific errors if elements exist
    Object.keys(errors).forEach(field => {
        const errorElement = document.querySelector(`#register-${field} + .text-red-500, [data-error="${field}"]`);
        if (errorElement) {
            const fieldErrors = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
            errorElement.textContent = fieldErrors[0];
            errorElement.classList.remove('hidden');
        }
    });
}

function getFieldDisplayName(field) {
    const fieldNames = {
        'name': 'Họ và tên',
        'email': 'Email',
        'phone': 'Số điện thoại',
        'password': 'Mật khẩu',
        'password_confirmation': 'Xác nhận mật khẩu',
        'gender': 'Giới tính',
        'address': 'Địa chỉ',
        'identify_number': 'Số CMND/CCCD'
    };
    return fieldNames[field] || field;
}

function clearRegistrationErrors() {
    const errorDiv = document.getElementById('registration-error');
    if (errorDiv) {
        errorDiv.classList.add('hidden');
    }

    // Clear field errors
    const fieldErrors = document.querySelectorAll('#step2-container .text-red-500');
    fieldErrors.forEach(error => {
        error.textContent = '';
        error.classList.add('hidden');
    });
}

function showSuccessMessage(message) {
    // Create temporary success notification
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            ${message}
        </div>
    `;

    document.body.appendChild(successDiv);

    // Auto remove after 3 seconds
    setTimeout(() => {
        if (successDiv.parentNode) {
            successDiv.parentNode.removeChild(successDiv);
        }
    }, 3000);
}

// Realtime phone checking và auto-fill
let phoneCheckTimeout;
// Updated selectors for step2 form
const phoneInput = document.querySelector('#step2-container input[name="phone"]');
const nameInput = document.querySelector('#step2-container input[name="name"]');
const emailInput = document.querySelector('#step2-container input[name="email"]');
const addressInput = document.querySelector('#step2-container input[name="address"]');
const identifyNumberInput = document.querySelector('#step2-container input[name="identify_number"]');
const genderSelect = document.querySelector('#step2-container select[name="gender"]');
const passwordInput = document.querySelector('#step2-container input[name="password"]');
const confirmPasswordInput = document.querySelector('#step2-container input[name="password_confirmation"]');

if (phoneInput) {
    phoneInput.addEventListener('input', function() {
        const phone = this.value.replace(/\D/g, '');

        clearTimeout(phoneCheckTimeout);
        resetAutoFillState();

        if (/^[0-9]{10,11}$/.test(phone)) {
            phoneCheckTimeout = setTimeout(() => checkPhoneRealtime(phone), 400);
        }
    });
}

function checkPhoneRealtime(phone) {
    // Show loading indicator
    showPhoneCheckLoading(true);

    fetch(`/api/customer/check-phone/${phone}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        showPhoneCheckLoading(false);

        if (data.success) {
            handlePhoneCheckResult(data);
        } else {
            showPhoneCheckError(data.message || 'Có lỗi xảy ra khi kiểm tra số điện thoại');
        }
    })
    .catch(error => {
        showPhoneCheckLoading(false);
        console.error('Phone check error:', error);
        showPhoneCheckError('Có lỗi xảy ra khi kiểm tra số điện thoại');
    });
}

function handlePhoneCheckResult(data) {
    switch (data.status) {
        case 'HAS_PASSWORD':
            showLoginBanner('Số điện thoại đã có tài khoản. Vui lòng đăng nhập.');
            lockAllFields(); // optional
            break;

        case 'NEEDS_PASSWORD':
            autoFillCustomerInfo(data.customer || {});
            showPasswordOnlyMode(); // chỉ hiện password + confirm
            break;

        case 'NOT_FOUND':
            enableFullRegisterMode(); // hiện toàn bộ form
            break;

        default:
            showPhoneCheckError('Không kiểm tra được số điện thoại. Vui lòng thử lại.');
    }
}

function autoFillCustomerInfo(customer) {
    console.log('🔄 Auto-filling customer info:', customer);

    // Set all available customer data (mapping backend field names)
    setValue('name', customer.name || '');
    setValue('email', customer.email || '');
    setValue('address', customer.addr || customer.address || ''); // Backend uses 'addr'
    setValue('identify_number', customer.identify_number || customer.cmnd || customer.cccd || '');

    // Set gender if available
    if (customer.gender !== undefined && customer.gender !== null) {
        setValue('gender', customer.gender.toString());
    }

    // Set phone in hidden field
    const phoneField = document.getElementById('final-phone');
    if (phoneField) {
        phoneField.value = customer.tel || currentPhone || '';
        console.log(`✅ Set final-phone = ${phoneField.value}`);
    }

    // Disable all fields except password fields for existing customers
    disableIfValue('name');
    disableIfValue('email');
    disableIfValue('address');
    disableIfValue('identify_number');
    disableIfValue('gender');

    // Show auto-fill message
    showAutoFillMessage();
}

function setValue(fieldName, value) {
    // Try step2 form first, then fallback to general ID
    let field = document.querySelector(`#step2-container input[name="${fieldName}"]`) ||
                document.querySelector(`#step2-container select[name="${fieldName}"]`) ||
                document.getElementById(fieldName) ||
                document.getElementById(`register-${fieldName}`);

    if (field) {
        if (value !== undefined && value !== null && value !== '') {
            field.value = value;
            console.log(`✅ Set ${fieldName} = ${value}`);
        } else {
            console.log(`⚪ Skipped ${fieldName} (empty value)`);
        }
    } else {
        console.warn(`❌ Field not found: ${fieldName}`);
    }
}

function disableIfValue(fieldName) {
    // Try step2 form first, then fallback to general ID
    let field = document.querySelector(`#step2-container input[name="${fieldName}"]`) ||
                document.querySelector(`#step2-container select[name="${fieldName}"]`) ||
                document.getElementById(fieldName) ||
                document.getElementById(`register-${fieldName}`);

    if (field) {
        // Disable field if it has value OR if we're in existing customer mode
        const shouldDisable = field.value || currentCustomerData;

        if (shouldDisable) {
            field.disabled = true;
            field.readOnly = true;
            field.removeAttribute('required'); // Remove required để tránh validation lỗi
            field.style.backgroundColor = '#f9fafb';
            field.style.color = '#6b7280';
            field.style.cursor = 'not-allowed';
            field.title = 'Thông tin này đã được xác thực và không thể chỉnh sửa';
            console.log(`🔒 Disabled field: ${fieldName} (value: ${field.value})`);
        }
    }
}

function showLoginBanner(message) {
    showPhoneCheckMessage(message, 'warning');
    // Có thể tự động chuyển sang tab login
    // switchToLogin();
}

function lockAllFields() {
    // Optional: disable all form fields
    const fields = ['name', 'email', 'phone', 'address', 'identify_number'];
    fields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            field.disabled = true;
        }
    });
}

function showPasswordOnlyMode() {
    showPhoneCheckMessage('Tài khoản đã tồn tại. Vui lòng tạo mật khẩu.', 'info');
    // Có thể ẩn các field khác và chỉ hiện password fields
    // Implementation tùy theo UI design
}

function enableFullRegisterMode() {
    showPhoneCheckMessage('Số điện thoại chưa được đăng ký. Bạn có thể tiếp tục đăng ký.', 'success');
    // Reset form về trạng thái ban đầu
    resetAutoFillState();
}

function resetAutoFillState() {
    const fields = ['name', 'email', 'phone', 'address', 'identify_number'];
    fields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            field.readOnly = false;
            field.disabled = false;
            field.style.backgroundColor = '';
            field.style.color = '';
            field.style.cursor = '';
        }
    });
    if (genderSelect) {
        genderSelect.style.backgroundColor = '';
        genderSelect.style.color = '';
        genderSelect.style.cursor = '';
        genderSelect.disabled = false;
        genderSelect.className = genderSelect.className.replace(/border-gray-300/, 'border-gray-200');
    }

    // Remove hidden gender input
    const hiddenGender = document.getElementById('hidden-gender');
    if (hiddenGender) {
        hiddenGender.remove();
    }

    hidePhoneCheckMessage();
    hideAutoFillMessage();
}

function restoreOriginalPlaceholder(field) {
    // Restore original placeholders based on field name
    if (field === nameInput) {
        field.placeholder = 'Nhập họ và tên đầy đủ';
    } else if (field === emailInput) {
        field.placeholder = 'example@email.com';
    } else if (field === addressInput) {
        field.placeholder = 'Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố';
    } else if (field === identifyNumberInput) {
        field.placeholder = 'Nhập số CMND hoặc CCCD';
    }
}

function showPhoneCheckLoading(show) {
    let indicator = document.getElementById('phoneCheckIndicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'phoneCheckIndicator';
        indicator.className = 'text-xs mt-1';
        phoneInput.parentNode.appendChild(indicator);
    }

    if (show) {
        indicator.innerHTML = '<span class="text-blue-500">🔍 Đang kiểm tra số điện thoại...</span>';
    } else {
        indicator.innerHTML = '';
    }
}

function showPhoneCheckMessage(message, type = 'info') {
    let messageDiv = document.getElementById('phoneCheckMessage');
    if (!messageDiv) {
        messageDiv = document.createElement('div');
        messageDiv.id = 'phoneCheckMessage';
        messageDiv.className = 'text-xs mt-1';
        phoneInput.parentNode.appendChild(messageDiv);
    }

    const colors = {
        success: 'text-green-600',
        warning: 'text-yellow-600',
        error: 'text-red-600',
        info: 'text-blue-600'
    };

    messageDiv.innerHTML = `<span class="${colors[type] || colors.info}">${message}</span>`;
}

function showPhoneCheckError(message) {
    showPhoneCheckMessage(message, 'error');
}

function hidePhoneCheckMessage() {
    const messageDiv = document.getElementById('phoneCheckMessage');
    if (messageDiv) {
        messageDiv.innerHTML = '';
    }
}

function showAutoFillMessage() {
    let messageDiv = document.getElementById('autoFillMessage');
    if (!messageDiv) {
        messageDiv = document.createElement('div');
        messageDiv.id = 'autoFillMessage';
        messageDiv.className = 'bg-gray-50 border border-gray-200 rounded-lg p-3 mb-4';

        // Insert before password section
        const passwordSection = document.querySelector('.space-y-4:last-child');
        if (passwordSection) {
            passwordSection.parentNode.insertBefore(messageDiv, passwordSection);
        }
    }

    messageDiv.innerHTML = `
        <div class="flex items-center text-sm text-gray-600">
            <svg class="h-4 w-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            Thông tin được bảo vệ và không thể chỉnh sửa. Chỉ cần tạo mật khẩu.
        </div>
    `;
}

function hideAutoFillMessage() {
    const messageDiv = document.getElementById('autoFillMessage');
    if (messageDiv) {
        messageDiv.remove();
    }
}

// Step Form Management
let currentStep = 1;

function initializeStepForm() {
    const step1Phone = document.getElementById('step1-phone');
    const step1Continue = document.getElementById('step1-continue');
    const step2Back = document.getElementById('step2-back');
    const switchToLoginBtn = document.getElementById('switch-to-login');

    // Step 1 phone input handler
    if (step1Phone) {
        step1Phone.addEventListener('input', function() {
            const digits = this.value.replace(/[^0-9]/g, '');
            this.value = digits;

            // Enable/disable continue button
            if (digits.length >= 10) {
                step1Continue.disabled = false;
                // Auto check phone for preview
                checkPhoneRealtimeStep1(digits);
            } else {
                step1Continue.disabled = true;
                clearStep1PhoneMessage();
            }
        });
        // Trigger initial state once in case user typed before listeners attached
        step1Phone.dispatchEvent(new Event('input'));
    }

    // Step 1 continue button
    if (step1Continue) {
        step1Continue.addEventListener('click', function() {
            const digits = (step1Phone?.value || '').replace(/[^0-9]/g, '');
            if (digits.length >= 10 && !this.disabled) {
                handleStep1Continue(digits);
            }
        });
    }

    // Step 2 back button
    if (step2Back) {
        step2Back.addEventListener('click', function() {
            goToStep(1);
        });
    }

    // Switch to login button
    if (switchToLoginBtn) {
        switchToLoginBtn.addEventListener('click', function() {
            try { window.switchToLogin && window.switchToLogin(); } catch (e) { console.error('switchToLogin error:', e); }
        });
    }
}

function goToStep(step) {
    const step1Container = document.getElementById('step1-container');
    const step2Container = document.getElementById('step2-container');
    const step1Indicator = document.getElementById('step1-indicator');
    const step2Indicator = document.getElementById('step2-indicator');
    const step1Label = document.getElementById('step1-label');
    const step2Label = document.getElementById('step2-label');
    const stepConnector = document.getElementById('step-connector');

    if (step === 1) {
        // Show step 1
        step1Container.classList.remove('hidden');
        step2Container.classList.add('hidden');

        // Update indicators
        step1Indicator.className = 'w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center text-base font-medium';
        step1Label.className = 'ml-3 text-base font-medium text-red-600';
        step2Indicator.className = 'w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-base font-medium';
        step2Label.className = 'ml-3 text-base text-gray-500';
        stepConnector.className = 'w-12 h-0.5 bg-gray-200';

        currentStep = 1;
    } else if (step === 2) {
        // Show step 2
        step1Container.classList.add('hidden');
        step2Container.classList.remove('hidden');

        // Update indicators
        step1Indicator.className = 'w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-medium';
        step1Label.className = 'ml-2 text-sm font-medium text-green-600';
        step2Indicator.className = 'w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center text-sm font-medium';
        step2Label.className = 'ml-2 text-sm font-medium text-red-600';
        stepConnector.className = 'w-8 h-0.5 bg-green-500';

        currentStep = 2;
    }
}

async function handleStep1Continue(phone) {
    const continueBtn = document.getElementById('step1-continue');
    const continueText = continueBtn.querySelector('.continue-text');
    const continueLoading = continueBtn.querySelector('.continue-loading');

    // Show loading
    continueText.classList.add('hidden');
    continueLoading.classList.remove('hidden');
    continueBtn.disabled = true;

    try {
        const response = await fetch(`/api/customer/check-phone/${phone}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        console.log('✅ Step1 API Response:', data);

        if (data.success) {
            // API trả về status ở level root, không phải data.data
            if (data.status === 'HAS_PASSWORD') {
                // Show has account message and auto-switch to login
                showHasAccountMessage();
                setTimeout(() => {
                    console.log('🔄 Auto-switching to login tab for existing account');
                    window.dispatchEvent(new CustomEvent('auth-modal-switch', { detail: { tab: 'login' } }));
                    // Pre-fill phone number in login form
                    const loginPhoneInput = document.querySelector('#loginForm input[name="login"]');
                    if (loginPhoneInput) {
                        loginPhoneInput.value = phone;
                        loginPhoneInput.focus();
                    }
                }, 2000);
            } else if (data.status === 'NEEDS_PASSWORD') {
                // Auto-fill and go to step 2
                currentCustomerData = data.customer;
                currentPhone = phone;
                setupStep2ForExistingCustomer();
                goToStep(2);
            } else {
                // New customer - go to step 2
                currentCustomerData = null;
                currentPhone = phone;
                setupStep2ForNewCustomer();
                goToStep(2);
            }
        } else {
            showStep1Error(data.message || 'Có lỗi xảy ra khi kiểm tra số điện thoại');
        }

    } catch (error) {
        console.error('Phone check error:', error);
        showStep1Error('Có lỗi xảy ra khi kiểm tra số điện thoại');
    } finally {
        // Hide loading
        continueText.classList.remove('hidden');
        continueLoading.classList.add('hidden');
        continueBtn.disabled = false;
    }
}

function checkPhoneRealtimeStep1(phone) {
    // Clear previous timeout
    clearTimeout(phoneCheckTimeout);

    // Show loading immediately
    showStep1PhoneLoading();

    // Set timeout để tránh spam API
    phoneCheckTimeout = setTimeout(async () => {
        try {
            const response = await fetch(`/api/customer/check-phone/${phone}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            console.log('🔍 Realtime check result:', data);
            showStep1PhonePreview(data);

        } catch (error) {
            console.error('Phone check error:', error);
            showStep1Error('Không thể kiểm tra số điện thoại');
        }
    }, 800); // Delay 800ms for preview
}

function showStep1PhoneLoading() {
    const messageDiv = document.getElementById('step1-phone-message');
    if (messageDiv) {
        messageDiv.className = 'text-sm mt-3 text-gray-600 bg-gray-50 border border-gray-200 rounded-lg p-3';
        messageDiv.innerHTML = '🔍 <strong>Đang kiểm tra số điện thoại...</strong>';
        messageDiv.classList.remove('hidden');
    }
}

// Step form helper functions
function showHasAccountMessage() {
    const messageDiv = document.getElementById('has-account-message');
    if (messageDiv) {
        messageDiv.classList.remove('hidden');
    }
}

function showStep1PhonePreview(data) {
    const messageDiv = document.getElementById('step1-phone-message');
    const continueBtn = document.getElementById('step1-continue');
    if (!messageDiv) return;

    if (data.success) {
        // API trả về status ở level root
        if (data.status === 'HAS_PASSWORD') {
            messageDiv.className = 'text-sm mt-3 text-red-700 bg-red-50 border border-red-200 rounded-lg p-3';
            messageDiv.innerHTML = '❌ <strong>Số điện thoại đã có tài khoản</strong><br><span class="text-xs">Vui lòng chuyển sang đăng nhập để truy cập tài khoản</span>';
            // Disable continue button for existing accounts
            if (continueBtn) {
                continueBtn.disabled = true;
                continueBtn.textContent = 'Đã có tài khoản - Vui lòng đăng nhập';
                continueBtn.className = 'w-full bg-gray-400 text-white py-4 font-semibold text-lg rounded-lg cursor-not-allowed';
            }
        } else if (data.status === 'NEEDS_PASSWORD') {
            messageDiv.className = 'text-sm mt-3 text-blue-700 bg-blue-50 border border-blue-200 rounded-lg p-3';
            messageDiv.innerHTML = '✅ <strong>Tìm thấy thông tin khách hàng</strong><br><span class="text-xs">Cần tạo mật khẩu để hoàn tất đăng ký</span>';
            // Enable continue for password creation
            if (continueBtn) {
                continueBtn.disabled = false;
                continueBtn.innerHTML = '<span class="continue-text">Tiếp tục</span><span class="continue-loading hidden"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Đang kiểm tra...</span>';
            }
        } else {
            messageDiv.className = 'text-sm mt-3 text-green-700 bg-green-50 border border-green-200 rounded-lg p-3';
            messageDiv.innerHTML = '✅ <strong>Số điện thoại có thể đăng ký</strong><br><span class="text-xs">Bấm Tiếp tục để điền thông tin đăng ký</span>';
            // Enable continue for new registration
            if (continueBtn) {
                continueBtn.disabled = false;
                continueBtn.innerHTML = '<span class="continue-text">Tiếp tục</span><span class="continue-loading hidden"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Đang kiểm tra...</span>';
            }
        }
        messageDiv.classList.remove('hidden');
    } else {
        messageDiv.className = 'text-sm mt-3 text-green-700 bg-green-50 border border-green-200 rounded-lg p-3';
        messageDiv.innerHTML = '✅ <strong>Số điện thoại có thể đăng ký</strong><br><span class="text-xs">Bấm Tiếp tục để điền thông tin đăng ký</span>';
        // Enable continue for new registration
        if (continueBtn) {
            continueBtn.disabled = false;
            continueBtn.innerHTML = '<span class="continue-text">Tiếp tục</span><span class="continue-loading hidden"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Đang kiểm tra...</span>';
        }
        messageDiv.classList.remove('hidden');
    }
}

function clearStep1PhoneMessage() {
    const messageDiv = document.getElementById('step1-phone-message');
    if (messageDiv) {
        messageDiv.classList.add('hidden');
    }
}

function showStep1Error(message) {
    const messageDiv = document.getElementById('step1-phone-message');
    if (messageDiv) {
        messageDiv.className = 'text-xs mt-2 text-red-600';
        messageDiv.textContent = '❌ ' + message;
        messageDiv.classList.remove('hidden');
    }
}

function setupStep2ForExistingCustomer() {
    console.log('🔧 Setting up Step 2 for existing customer:', currentCustomerData);

    // Update step 2 header
    const title = document.getElementById('step2-title');
    const subtitle = document.getElementById('step2-subtitle');
    const notification = document.getElementById('autofill-notification');
    const finalPhone = document.getElementById('final-phone');

    if (title) {
        title.textContent = 'Tạo mật khẩu';
        console.log('✅ Updated title');
    }
    if (subtitle) {
        subtitle.textContent = 'Tài khoản đã tồn tại, chỉ cần tạo mật khẩu';
        console.log('✅ Updated subtitle');
    }
    if (notification) {
        notification.classList.remove('hidden');
        console.log('✅ Showed notification');
    }
    if (finalPhone) {
        finalPhone.value = currentPhone;
        console.log('✅ Set final phone:', currentPhone);
    }

    // Auto-fill customer data
    if (currentCustomerData) {
        autoFillCustomerInfo(currentCustomerData);
    } else {
        console.warn('❌ No customer data to auto-fill');
    }
}

function setupStep2ForNewCustomer() {
    // Update step 2 header
    const title = document.getElementById('step2-title');
    const subtitle = document.getElementById('step2-subtitle');
    const notification = document.getElementById('autofill-notification');
    const finalPhone = document.getElementById('final-phone');

    if (title) title.textContent = 'Hoàn tất đăng ký';
    if (subtitle) subtitle.textContent = 'Điền thông tin để tạo tài khoản mới';
    if (notification) notification.classList.add('hidden');
    if (finalPhone) finalPhone.value = currentPhone;

    // Clear any autofilled data from previous customer
    clearAutoFilledData();
}

function clearAutoFilledData() {
    // Clear and reset all form fields to editable state
    console.log('🧹 Clearing auto-filled data');

    const fields = [
        { name: 'name', placeholder: 'Nhập họ và tên đầy đủ' },
        { name: 'email', placeholder: 'example@email.com' },
        { name: 'address', placeholder: 'Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố' },
        { name: 'identify_number', placeholder: 'Nhập số CMND hoặc CCCD' }
    ];

    fields.forEach(fieldInfo => {
        const field = document.querySelector(`#step2-container input[name="${fieldInfo.name}"]`) ||
                     document.getElementById(`register-${fieldInfo.name}`);
        if (field) {
            field.value = '';
            field.readOnly = false;
            field.disabled = false;
            field.placeholder = fieldInfo.placeholder;
            field.className = field.className.replace(/border-gray-300/, 'border-gray-200');
            field.style.backgroundColor = '';
            field.style.color = '';
            field.style.cursor = '';
        }
    });

    // Gender select
    const genderField = document.querySelector('#step2-container select[name="gender"]');
    if (genderField) {
        genderField.value = '0'; // Default to male
        genderField.disabled = false;
        genderField.style.backgroundColor = '';
        genderField.style.color = '';
        genderField.style.cursor = '';
    }

    // Clear phone field
    const phoneField = document.getElementById('final-phone');
    if (phoneField) {
        phoneField.value = currentPhone || '';
    }
}

// Initialize step form when modal opens
// Safe init even if DOMContentLoaded already fired; also init on first modal open
(function initStepFormSafely(){
    const doInit = () => { if (!window.__authStepInitialized) { window.__authStepInitialized = true; try { initializeStepForm(); } catch(e){ console.error('Init step error:', e); } } };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', doInit, { once: true });
    } else {
        setTimeout(doInit, 0);
    }
    window.addEventListener('auth-modal-open', doInit, { once: true });
})();

// Keep previous session-based auto open
(function(){
    @if (session('modal_login_error'))
        window.dispatchEvent(new CustomEvent('auth-modal-open', { detail: { tab: 'login' } }));
    @elseif (session('modal_register_error'))
        window.dispatchEvent(new CustomEvent('auth-modal-open', { detail: { tab: 'register' } }));
    @endif
})();

// Function to show password creation form
function showPasswordCreationForm(customerName, message) {
    console.log('🔑 Showing password creation form for:', customerName);

    // Hide ALL other forms
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    if (loginForm) {
        loginForm.classList.add('hidden');
        console.log('✅ Hidden login form');
    }
    if (registerForm) {
        registerForm.classList.add('hidden');
        console.log('✅ Hidden register form');
    }

    // Show password creation form
    const createPasswordForm = document.getElementById('createPasswordForm');
    if (createPasswordForm) {
        createPasswordForm.classList.remove('hidden');

        // Update modal title
        document.getElementById('modalTitle').textContent = 'Tạo mật khẩu';

        // Update customer name
        const customerDisplayName = document.getElementById('customerDisplayName');
        if (customerDisplayName) {
            customerDisplayName.textContent = customerName;
        }

        // Show customer info
        const customerInfoDisplay = document.getElementById('customerInfoDisplay');
        if (customerInfoDisplay) {
            customerInfoDisplay.classList.remove('hidden');
        }

        // Skip verification step and go directly to password creation
        const verificationStep = document.getElementById('verificationStep');
        const passwordCreationStep = document.getElementById('passwordCreationStep');

        if (verificationStep) {
            verificationStep.classList.add('hidden');
        }
        if (passwordCreationStep) {
            passwordCreationStep.classList.remove('hidden');
        }

        // Update form action - đảm bảo action đúng
        const passwordForm = document.getElementById('passwordCreationForm');
        if (passwordForm) {
            passwordForm.action = '/modal/tao-mat-khau';
            console.log('🔑 Updated form action to:', passwordForm.action);
        }

        // Show message
        if (message) {
            showSuccessMessage(message);
        }

        // Re-attach event listener sau khi show form
        setTimeout(() => {
            const form = document.getElementById('passwordCreationForm');
            if (form) {
                console.log('🔑 Form found after show, action:', form.action);
                // Remove existing listeners và add lại để đảm bảo hoạt động
                const newForm = form.cloneNode(true);
                form.parentNode.replaceChild(newForm, form);

                newForm.addEventListener('submit', function(e) {
                    console.log('🔑 Password form submitted (new listener)!');
                    e.preventDefault();
                    handlePasswordCreation(this);
                });
            }
        }, 100);
    }
}

// Function to handle password creation
async function handlePasswordCreation(form) {
    console.log('🔑 Creating password...');
    console.log('🔑 Form action:', form.action);
    console.log('🔑 Form method:', form.method);

    const formData = new FormData(form);

    // Debug form data
    console.log('🔑 Form data:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    // Show loading
    submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Đang tạo mật khẩu...';
    submitButton.disabled = true;

    try {
        console.log('🔑 Sending request to:', form.action);
        console.log('🔑 Request headers:', {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        });

        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        console.log('🔑 Response status:', response.status);
        console.log('🔑 Response headers:', response.headers);

        if (!response.ok) {
            console.error('🔑 Response not OK:', response.status, response.statusText);
            const errorText = await response.text();
            console.error('🔑 Error response:', errorText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            console.error('🔑 Response is not JSON:', contentType);
            const textResponse = await response.text();
            console.error('🔑 Response text:', textResponse);
            throw new Error('Response is not JSON');
        }

        const data = await response.json();
        console.log('🔑 Password creation response:', data);

        if (data.success) {
            console.log('✅ Password created successfully');

            // Close modal
            closeAuthModal();

            // Show success message
            showSuccessMessage(data.message || 'Tạo mật khẩu thành công!');

            // Trigger Livewire refresh for auth components
            if (window.Livewire) {
                window.Livewire.dispatch('customer-password-created');
                window.Livewire.dispatch('customer-logged-in');
            }

            // Emit global auth events
            window.dispatchEvent(new CustomEvent('customer-logged-in', {
                detail: { user: data.user }
            }));

            // Reload page to update auth state
            setTimeout(() => {
                window.location.reload();
            }, 800);
        } else {
            console.log('❌ Password creation failed:', data);

            // Handle validation errors
            if (data.errors) {
                displayPasswordErrors(data.errors);
            } else {
                showErrorMessage(data.message || 'Có lỗi xảy ra khi tạo mật khẩu.');
            }
        }
    } catch (error) {
        console.error('❌ Password creation error:', error);
        showErrorMessage('Có lỗi xảy ra. Vui lòng thử lại sau.');
    } finally {
        // Reset button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }
}

// Function to display password validation errors
function displayPasswordErrors(errors) {
    // Clear previous errors
    document.querySelectorAll('.password-error').forEach(el => el.remove());

    Object.keys(errors).forEach(field => {
        const input = document.querySelector(`#passwordCreationForm input[name="${field}"]`);
        if (input) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'password-error text-red-500 text-sm mt-1';
            errorDiv.textContent = errors[field][0];
            input.parentNode.appendChild(errorDiv);
        }
    });
}
</script>
