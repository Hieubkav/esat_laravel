<div class="space-y-3">
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-2.5 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200">
            <button wire:click="switchTab('info')"
                    class="flex-1 py-2.5 px-3 text-sm font-medium transition-colors {{ $activeTab === 'info' ? 'bg-red-50 text-red-600 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900' }}">
                Thông tin
            </button>
            <button wire:click="switchTab('password')"
                    class="flex-1 py-2.5 px-3 text-sm font-medium transition-colors {{ $activeTab === 'password' ? 'bg-red-50 text-red-600 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900' }}">
                Mật khẩu
            </button>
        </div>

        <!-- Tab Content -->
        <div class="p-4 sm:p-5">
            <!-- Info Tab -->
            @if($activeTab === 'info')
                <!-- Thông báo về MShopKeeper -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-blue-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800">Thông tin khách hàng MShopKeeper</h4>
                            <p class="text-xs text-blue-600 mt-1">Thông tin cá nhân được quản lý bởi hệ thống MShopKeeper. Để cập nhật thông tin, vui lòng liên hệ admin.</p>
                        </div>
                    </div>
                </div>

                <form wire:submit="updateInfo" class="space-y-3">
                    <input type="text"
                           wire:model="name"
                           readonly
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                           placeholder="Họ và tên (chỉ đọc)">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                    @if($email)
                        <input type="email"
                               value="{{ $email }}"
                               readonly
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                               placeholder="Email (không thể thay đổi)">
                    @endif

                    @if($phone)
                        <input type="tel"
                               value="{{ $phone }}"
                               readonly
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                               placeholder="Số điện thoại (không thể thay đổi)">
                    @endif

                    @if($gender !== '')
                        <input type="text"
                               value="{{ $gender == 0 ? 'Nam' : ($gender == 1 ? 'Nữ' : 'Chưa xác định') }}"
                               readonly
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                               placeholder="Giới tính (không thể thay đổi)">
                    @endif

                    @if($identify_number)
                        <input type="text"
                               value="{{ $identify_number }}"
                               readonly
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                               placeholder="Số CMND/CCCD (không thể thay đổi)">
                    @endif

                    <textarea wire:model="address"
                              rows="2"
                              readonly
                              class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed resize-none"
                              placeholder="Địa chỉ (chỉ đọc)"></textarea>
                    @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                    <button type="submit"
                            disabled
                            class="w-full bg-gray-400 text-white py-2 text-sm font-medium rounded-lg cursor-not-allowed">
                        Thông tin được quản lý bởi MShopKeeper
                    </button>
                </form>
            @endif

            <!-- Password Tab -->
            @if($activeTab === 'password')
                <form wire:submit="updatePassword" class="space-y-3">
                    <input type="password"
                           wire:model="current_password"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Mật khẩu hiện tại">
                    @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                    <input type="password"
                           wire:model="new_password"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Mật khẩu mới">
                    @error('new_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                    <input type="password"
                           wire:model="new_password_confirmation"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Xác nhận mật khẩu mới">

                    <button type="submit"
                            class="w-full bg-red-600 text-white py-2 text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <span wire:loading.remove wire:target="updatePassword">Đổi mật khẩu</span>
                        <span wire:loading wire:target="updatePassword">Đang cập nhật...</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
