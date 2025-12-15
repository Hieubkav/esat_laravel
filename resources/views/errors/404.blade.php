@extends('layouts.error')
@section('title', 'Trang không tìm thấy - 404')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="text-center">
        <h1 class="text-8xl font-bold text-red-600 mb-4">404</h1>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Trang không tìm thấy</h2>

        <div class="flex gap-4 justify-center">
            <a href="/" class="px-6 py-3 bg-red-600 text-white rounded hover:bg-red-700">Về trang chủ</a>
            <button onclick="history.back()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Quay lại</button>
        </div>
    </div>
</div>
@endsection
