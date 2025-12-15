@extends('layouts.error')
@section('title', 'Phiên làm việc hết hạn - 419')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="text-center">
        <h1 class="text-8xl font-bold text-yellow-600 mb-4">419</h1>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Phiên làm việc hết hạn</h2>

        <div class="flex gap-4 justify-center">
            <button onclick="location.reload()" class="px-6 py-3 bg-yellow-600 text-white rounded hover:bg-yellow-700">Tải lại trang</button>
            <a href="/" class="px-6 py-3 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Về trang chủ</a>
        </div>
    </div>
</div>
@endsection
