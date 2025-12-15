@extends('layouts.shop')

@section('title', $employee->name . ' - Thông tin nhân viên')
@section('description', 'Thông tin chi tiết về ' . $employee->name . ', ' . $employee->position . ' tại Vũ Phúc Baking')

@push('styles')
<style>
    .profile-section {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .profile-card {
        background: white;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
    }

    .red-accent {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }

    .red-text {
        color: #dc2626;
    }

    .contact-card {
        background: white;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .contact-card:hover {
        border-color: #dc2626;
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.1);
        transform: translateY(-2px);
    }

    .avatar-container {
        position: relative;
        display: inline-block;
    }

    .avatar-ring {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        padding: 6px;
        border-radius: 50%;
    }

    .description-section {
        background: #f8fafc;
        border-left: 4px solid #dc2626;
    }

    .gallery-item {
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .gallery-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    }

    .gallery-item img {
        transition: transform 0.3s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.05);
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

    .btn-outline {
        background: white;
        color: #374151;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .btn-outline:hover {
        border-color: #dc2626;
        color: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.1);
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, #dc2626, transparent);
        margin: 3rem 0;
    }

    .heading-gradient {
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Image Popup Styles */
    .image-popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .image-popup.active {
        opacity: 1;
        visibility: visible;
    }

    .image-popup-content {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        transform: scale(0.8);
        transition: transform 0.3s ease;
    }

    .image-popup.active .image-popup-content {
        transform: scale(1);
    }

    .image-popup img {
        width: 100%;
        height: auto;
        display: block;
    }

    .image-popup-close {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 18px;
        color: #374151;
        transition: all 0.2s ease;
    }

    .image-popup-close:hover {
        background: white;
        transform: scale(1.1);
    }

    .gallery-item {
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .gallery-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    }

    .gallery-item img {
        transition: transform 0.3s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.05);
    }

    /* Responsive text handling */
    .contact-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    @media (max-width: 768px) {
        .contact-text {
            font-size: 1.125rem;
        }
    }

    @media (max-width: 640px) {
        .contact-text {
            font-size: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen profile-section">
    <div class="max-w-7xl mx-auto px-4 py-16">
        <!-- Main Profile Section -->
        <div class="profile-card rounded-3xl p-8 md:p-16 mb-16">
            <div class="grid lg:grid-cols-3 gap-12 items-start">
                <!-- Avatar Section -->
                <div class="lg:col-span-1 text-center">
                    <div class="avatar-container mb-8">
                        <div class="avatar-ring">
                            <div class="w-80 h-80 md:w-96 md:h-96 rounded-full overflow-hidden bg-white mx-auto">
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
                    </div>
                </div>

                <!-- Info Section -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Name & Position -->
                    <div class="text-center lg:text-left">
                        <h1 class="text-5xl md:text-6xl font-light heading-gradient mb-6">
                            {{ $employee->name }}
                        </h1>
                        <p class="text-2xl red-text font-semibold mb-8">
                            {{ $employee->position }}
                        </p>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-6 mb-8">
                        @if($employee->phone)
                        <div class="contact-card rounded-2xl p-6 flex items-center space-x-4">
                            <div class="w-14 h-14 red-accent rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-white text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">Điện thoại</p>
                                <a href="tel:{{ $employee->phone }}" class="text-gray-900 font-bold text-xl hover:text-red-600 transition-colors contact-text">
                                    {{ $employee->phone }}
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($employee->email)
                        <div class="contact-card rounded-2xl p-6 flex items-center space-x-4">
                            <div class="w-14 h-14 red-accent rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-white text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">Email</p>
                                <a href="mailto:{{ $employee->email }}" class="text-gray-900 font-bold text-xl hover:text-red-600 transition-colors contact-text">
                                    {{ $employee->email }}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Section - Full Width -->
        @if($employee->description)
        <div class="profile-card rounded-3xl p-12 mb-16">
            <div class="max-w-4xl mx-auto">
                <div class="description-section rounded-2xl p-10">
                    <div class="text-gray-700 leading-relaxed prose prose-xl max-w-none">
                        {!! $employee->description !!}
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Section Divider -->
        <div class="section-divider"></div>

        <!-- Gallery Section -->
        @if($employee->images && $employee->images->count() > 0)
        <div class="profile-card rounded-3xl p-12 mb-16">
            <div class="text-center mb-12">
                <h3 class="text-3xl font-light heading-gradient mb-4">
                    Hình ảnh
                </h3>
                <div class="w-16 h-0.5 red-accent mx-auto"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($employee->images as $image)
                <div class="gallery-item rounded-2xl overflow-hidden bg-white shadow-lg"
                     onclick="openImagePopup('{{ asset('storage/' . $image->image_link) }}', '{{ $image->alt_text ?: $employee->name }}', '{{ $image->caption ?? '' }}')">
                    <img src="{{ asset('storage/' . $image->image_link) }}"
                         alt="{{ $image->alt_text ?: $employee->name }}"
                         class="w-full h-64 object-cover">
                    @if($image->caption)
                    <div class="p-6 bg-white">
                        <p class="text-gray-600 text-base leading-relaxed">{{ $image->caption }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Back Button -->
        <div class="text-center">
            <a href="{{ route('storeFront') }}"
               class="btn-outline inline-flex items-center space-x-4 px-12 py-5 rounded-2xl font-semibold text-lg">
                <i class="fas fa-arrow-left text-xl"></i>
                <span>Về trang chủ</span>
            </a>
        </div>
    </div>
</div>

<!-- Image Popup -->
<div id="imagePopup" class="image-popup" onclick="closeImagePopup()">
    <div class="image-popup-content" onclick="event.stopPropagation()">
        <button class="image-popup-close" onclick="closeImagePopup()">
            <i class="fas fa-times"></i>
        </button>
        <img id="popupImage" src="" alt="">
        <div id="popupCaption" class="p-6 bg-white" style="display: none;">
            <p class="text-gray-600 text-lg leading-relaxed"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openImagePopup(imageSrc, imageAlt, imageCaption) {
    const popup = document.getElementById('imagePopup');
    const popupImage = document.getElementById('popupImage');
    const popupCaption = document.getElementById('popupCaption');

    popupImage.src = imageSrc;
    popupImage.alt = imageAlt;

    if (imageCaption && imageCaption.trim() !== '') {
        popupCaption.querySelector('p').textContent = imageCaption;
        popupCaption.style.display = 'block';
    } else {
        popupCaption.style.display = 'none';
    }

    popup.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeImagePopup() {
    const popup = document.getElementById('imagePopup');
    popup.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close popup with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImagePopup();
    }
});
</script>
@endpush

@endsection
