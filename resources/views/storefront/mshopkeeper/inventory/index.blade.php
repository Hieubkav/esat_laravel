@extends('layouts.shop')

@section('title', 'Kho Hàng - Sản Phẩm MShopKeeper')

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.filter-card {
    background: white;
    border: 1px solid #f1f5f9;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.filter-card:hover {
    border-color: #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.filter-btn {
    color: #64748b;
    transition: all 0.2s ease;
}

.filter-btn:hover {
    background-color: #fef2f2;
    color: #dc2626;
}

.filter-btn.active {
    background-color: #fef2f2;
    color: #dc2626;
    font-weight: 600;
}

.product-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-card:hover {
    transform: translateY(-4px);
}
</style>
@endpush

@section('content')
    @livewire('mshopkeeper-inventory-filter')
@endsection
