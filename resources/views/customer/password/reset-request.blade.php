@extends('layouts.storefront')

@section('title', 'Quên mật khẩu')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Quên mật khẩu</h2>
            <p class="text-sm text-gray-600">Nhập số điện thoại để nhận link đặt lại mật khẩu</p>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 rounded-md p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

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

            <form method="POST" action="{{ route('customer.password.email') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Số điện thoại <span class="text-red-500">*</span>
                    </label>
                    <input type="tel"
                           name="phone"
                           id="phone"
                           value="{{ old('phone') }}"
                           required
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                           placeholder="Nhập số điện thoại đã đăng ký">
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-800">
                                Link đặt lại mật khẩu sẽ được gửi đến email đã đăng ký của bạn.
                                Nếu tài khoản chưa có email, vui lòng liên hệ admin để được hỗ trợ.
                            </p>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-red-600 text-white py-2 text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                    Gửi link đặt lại mật khẩu
                </button>
            </form>
        </div>

        <!-- Links -->
        <div class="text-center space-y-2">
            <a href="{{ route('customer.login') }}" class="text-sm text-gray-600 hover:text-gray-800">← Quay lại đăng nhập</a>
            <div>
                <span class="text-sm text-gray-500">Chưa có tài khoản? </span>
                <a href="{{ route('customer.register') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">Đăng ký ngay</a>
            </div>
            <div>
                <a href="{{ route('storeFront') }}" class="text-xs text-gray-500 hover:text-gray-700">Trang chủ</a>
            </div>
        </div>
    </div>
</div>
@endsection
