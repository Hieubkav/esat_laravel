@extends('layouts.shop')

@section('content')
<div class="min-h-screen bg-white py-4 px-3">
    <div class="max-w-md mx-auto">
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-gray-900">Tài khoản</h1>
        </div>

        @livewire('customer.profile-manager')

        <!-- Back to Home -->
        <div class="mt-4 text-center">
            <a href="{{ route('storeFront') }}" class="text-xs text-gray-500 hover:text-gray-700">← Trang chủ</a>
        </div>
    </div>
</div>
@endsection
