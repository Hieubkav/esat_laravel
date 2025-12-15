@extends('layouts.error')
@section('title', 'Dịch vụ tạm ngưng - 503')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="text-center">
        <h1 class="text-8xl font-bold text-blue-600 mb-4">503</h1>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Dịch vụ tạm ngưng</h2>

        <div class="flex gap-4 justify-center">
            <a href="/" class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700">Về trang chủ</a>
            <button onclick="location.reload()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Thử lại</button>
        </div>
    </div>
</div>
@endsection
