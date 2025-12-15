<div class="container mx-auto px-4">
    <!-- Gallery Header -->
    <div class="text-center max-w-4xl mx-auto mb-16">
        <span class="inline-block py-1 px-3 text-xs font-semibold bg-red-100 text-red-800 rounded-full tracking-wider">BỘ SƯU TẬP ĐẶC BIỆT</span>
        <h2 class="mt-4 text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">Sản phẩm <span class="text-red-700">nổi bật</span></h2>
        <p class="mt-6 text-lg text-gray-600">Khám phá bộ sưu tập sản phẩm độc đáo và chất lượng cao nhất từ Vũ Phúc Baking</p>
    </div>
    
    <!-- Interactive Image Gallery with Masonry Layout -->
    <div class="masonry-gallery">
        <!-- First Column -->
        <div class="gallery-column">
            <div class="gallery-item hover-zoom">
                <img src="{{ asset('images/product-gallery-1.jpg') }}" alt="Bánh kem cao cấp" class="rounded-xl shadow-lg transform transition duration-500" onerror="this.src='https://images.unsplash.com/photo-1464349153735-7db50ed83c84?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80';">
                <div class="gallery-overlay">
                    <h3 class="gallery-title">Bánh kem cao cấp</h3>
                    <p class="gallery-description">Bánh kem tươi nhẹ với bột Rich's</p>
                </div>
            </div>
            <div class="gallery-item hover-zoom">
                <img src="{{ asset('images/product-gallery-2.jpg') }}" alt="Whipping Cream" class="rounded-xl shadow-lg transform transition duration-500" onerror="this.src='https://images.unsplash.com/photo-1488477304112-4944851de03d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80';">
                <div class="gallery-overlay">
                    <h3 class="gallery-title">Whipping Cream</h3>
                    <p class="gallery-description">Nguyên liệu cao cấp nhập khẩu</p>
                </div>
            </div>
        </div>
        
        <!-- Second Column -->
        <div class="gallery-column">
            <div class="gallery-item hover-zoom">
                <img src="{{ asset('images/product-gallery-3.jpg') }}" alt="Bánh mì Pháp" class="rounded-xl shadow-lg transform transition duration-500" onerror="this.src='https://images.unsplash.com/photo-1608198093002-ad4e005484ec?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80';">
                <div class="gallery-overlay">
                    <h3 class="gallery-title">Bánh mì Pháp</h3>
                    <p class="gallery-description">Làm từ bột mì hảo hạng</p>
                </div>
            </div>
            <div class="gallery-item hover-zoom">
                <img src="{{ asset('images/product-gallery-4.jpg') }}" alt="Chocolate nguyên chất" class="rounded-xl shadow-lg transform transition duration-500" onerror="this.src='https://images.unsplash.com/photo-1511381939415-e44015466834?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80';">
                <div class="gallery-overlay">
                    <h3 class="gallery-title">Chocolate nguyên chất</h3>
                    <p class="gallery-description">Nhập khẩu từ châu Âu</p>
                </div>
            </div>
            <div class="gallery-item hover-zoom">
                <img src="{{ asset('images/product-gallery-5.jpg') }}" alt="Cake Topper" class="rounded-xl shadow-lg transform transition duration-500" onerror="this.src='https://images.unsplash.com/photo-1606890737304-57a1ca8a5b62?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80';">
                <div class="gallery-overlay">
                    <h3 class="gallery-title">Cake Topper</h3>
                    <p class="gallery-description">Phụ kiện trang trí bánh</p>
                </div>
            </div>
        </div>
        
        <!-- Third Column -->
        <div class="gallery-column">
            <div class="gallery-item hover-zoom">
                <img src="{{ asset('images/product-gallery-6.jpg') }}" alt="Butter Cream" class="rounded-xl shadow-lg transform transition duration-500" onerror="this.src='https://images.unsplash.com/photo-1519869325930-281384150729?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80';">
                <div class="gallery-overlay">
                    <h3 class="gallery-title">Butter Cream</h3>
                    <p class="gallery-description">Hương vị bơ thơm ngon</p>
                </div>
            </div>
            <div class="gallery-item hover-zoom">
                <img src="{{ asset('images/product-gallery-7.jpg') }}" alt="Bánh Cupcake" class="rounded-xl shadow-lg transform transition duration-500" onerror="this.src='https://images.unsplash.com/photo-1599785209707-a456fc1337bb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80';">
                <div class="gallery-overlay">
                    <h3 class="gallery-title">Bánh Cupcake</h3>
                    <p class="gallery-description">Các loại cupcake mini</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- CTA Button -->
    <div class="text-center mt-12">
        <a href="{{ route('products.categories') }}" class="inline-flex items-center px-6 py-3 border-2 border-red-700 text-red-700 font-medium rounded-lg hover:bg-red-700 hover:text-white transition-colors group">
            <span>Xem tất cả sản phẩm</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</div>

@push('styles')
<style>
    /* Masonry Gallery Styles */
    .masonry-gallery {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }
    
    @media (min-width: 640px) {
        .masonry-gallery {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
    }
    
    @media (min-width: 1024px) {
        .masonry-gallery {
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
    }
    
    .gallery-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    
    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
    }
    
    .gallery-item img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }
    
    .hover-zoom:hover img {
        transform: scale(1.05);
    }
    
    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 20px;
        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
        color: white;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }
    
    .gallery-item:hover .gallery-overlay {
        opacity: 1;
        transform: translateY(0);
    }
    
    .gallery-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 5px 0;
    }
    
    .gallery-description {
        font-size: 14px;
        opacity: 0.9;
    }
</style>
@endpush