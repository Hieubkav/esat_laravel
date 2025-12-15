@extends('layouts.storefront')

@section('title', 'Tạo mật khẩu')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Tạo mật khẩu</h2>
            <p class="text-sm text-gray-600">Chúng tôi tìm thấy thông tin của bạn trong hệ thống</p>
        </div>

        <!-- Customer Info Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">{{ $mshopkeeperCustomer['Name'] ?? 'Khách hàng' }}</h3>
                    <div class="text-sm text-blue-600">
                        <p>SĐT: {{ $mshopkeeperCustomer['Tel'] ?? 'N/A' }}</p>
                        @if(!empty($mshopkeeperCustomer['Email']))
                            <p>Email: {{ $mshopkeeperCustomer['Email'] }}</p>
                        @endif
                        @if(isset($mshopkeeperCustomer['Gender']))
                            <p>Giới tính: {{ $mshopkeeperCustomer['Gender'] == 0 ? 'Nam' : ($mshopkeeperCustomer['Gender'] == 1 ? 'Nữ' : 'Chưa xác định') }}</p>
                        @endif
                        @if(!empty($mshopkeeperCustomer['Addr']))
                            <p>Địa chỉ: {{ $mshopkeeperCustomer['Addr'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Có lỗi xảy ra:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('customer.create-password.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Mật khẩu mới <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           required
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Xác nhận mật khẩu <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           required
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Nhập lại mật khẩu">
                </div>

                <button type="submit"
                        class="w-full bg-red-600 text-white py-2 text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                    Tạo mật khẩu và đăng nhập
                </button>
            </form>
        </div>

        <!-- Links -->
        <div class="text-center space-y-2">
            <a href="{{ route('storeFront') }}" class="text-sm text-gray-600 hover:text-gray-800">← Quay lại trang chủ</a>
            <div>
                <a href="{{ route('storeFront') }}" class="text-xs text-gray-500 hover:text-gray-700">Trang chủ</a>
            </div>
        </div>
    </div>
</div>
@endsection
