@extends('layouts.shop')

@section('title', 'Danh sách nhân viên - ' . ($settings->site_name ?? 'Vũ Phúc Baking'))
@section('description', 'Danh sách toàn bộ nhân viên của ' . ($settings->site_name ?? 'Vũ Phúc Baking'))

@push('styles')
<style>
    .employee-section {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .employee-card {
        background: white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-radius: 20px;
        overflow: hidden;
    }

    .employee-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    }

    .red-accent {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }

    .red-text {
        color: #dc2626;
    }

    .avatar-ring {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        padding: 4px;
        border-radius: 50%;
    }

    .btn-primary {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
    }

    .heading-gradient {
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .employee-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
    }

    @media (max-width: 640px) {
        .employee-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
    }

    .contact-info {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .contact-info a {
        color: #374151;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .contact-info a:hover {
        color: #dc2626;
    }

    .employee-position {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #dc2626;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        text-align: center;
        margin-bottom: 1rem;
    }

    .back-button {
        background: white;
        color: #374151;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .back-button:hover {
        border-color: #dc2626;
        color: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen employee-section">
    <div class="max-w-7xl mx-auto px-4 py-16">
        <!-- Header Section -->
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-light heading-gradient mb-6">
                Đội ngũ nhân viên
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Gặp gỡ đội ngũ chuyên nghiệp và tận tâm của {{ $settings->site_name ?? 'Vũ Phúc Baking' }}
            </p>
            <div class="w-24 h-1 red-accent mx-auto mt-8 rounded-full"></div>
        </div>

        <!-- Employee Grid -->
        @if($employees && $employees->count() > 0)
        <div class="employee-grid mb-16">
            @foreach($employees as $employee)
            <div class="employee-card">
                <!-- Avatar Section -->
                <div class="p-8 text-center">
                    <div class="avatar-ring inline-block mb-6">
                        <div class="w-32 h-32 rounded-full overflow-hidden bg-white">
                            @if($employee->image_link)
                                <img src="{{ asset('storage/' . $employee->image_link) }}"
                                     alt="{{ $employee->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <img src="{{ \App\Helpers\PlaceholderHelper::getPlaceholderImage('employee') }}"
                                     alt="{{ $employee->name }}"
                                     class="w-full h-full object-cover">
                            @endif
                        </div>
                    </div>

                    <!-- Name -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">
                        {{ $employee->name }}
                    </h3>

                    <!-- Position -->
                    <div class="employee-position">
                        {{ $employee->position }}
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-2 mb-6">
                        @if($employee->phone)
                        <div class="contact-info">
                            <i class="fas fa-phone text-red-600 mr-2"></i>
                            <a href="tel:{{ $employee->phone }}" class="truncate inline-block max-w-full">
                                {{ $employee->phone }}
                            </a>
                        </div>
                        @endif

                        @if($employee->email)
                        <div class="contact-info">
                            <i class="fas fa-envelope text-red-600 mr-2"></i>
                            <a href="mailto:{{ $employee->email }}" class="truncate inline-block max-w-full">
                                {{ $employee->email }}
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- View Profile Button -->
                    <a href="{{ route('employee.profile', $employee->slug) }}"
                       class="btn-primary inline-flex items-center space-x-2 px-6 py-3 rounded-xl font-semibold">
                        <i class="fas fa-user text-sm"></i>
                        <span>Xem thông tin</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-users text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4">Chưa có nhân viên nào</h3>
            <p class="text-gray-600">Danh sách nhân viên sẽ được cập nhật sớm.</p>
        </div>
        @endif

        <!-- Back Button -->
        <div class="text-center">
            <a href="{{ route('storeFront') }}"
               class="back-button inline-flex items-center space-x-4 px-12 py-5 rounded-2xl font-semibold text-lg">
                <i class="fas fa-arrow-left text-xl"></i>
                <span>Về trang chủ</span>
            </a>
        </div>
    </div>
</div>
@endsection
