<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Hướng dẫn quản lý khách hàng MShopKeeper</h3>
        <p class="text-sm text-gray-600">Hướng dẫn sử dụng trang quản lý khách hàng từ hệ thống MShopKeeper</p>
    </div>

    <!-- Search Guide -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h4 class="text-base font-medium text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Tìm kiếm khách hàng
        </h4>
        <div class="space-y-3 text-sm text-gray-600">
            <p><strong>Tìm kiếm nhanh:</strong> Sử dụng ô tìm kiếm ở góc phải bảng để tìm theo tên, email, hoặc số điện thoại.</p>
            <p><strong>Tìm kiếm nâng cao:</strong> Nhấn nút "Tìm kiếm khách hàng" để tìm kiếm chính xác theo số điện thoại hoặc email.</p>
            <p><strong>Lưu ý:</strong> Tìm kiếm nâng cao sử dụng API riêng và có thể cho kết quả chính xác hơn.</p>
        </div>
    </div>

    <!-- Filter Guide -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h4 class="text-base font-medium text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Bộ lọc và sắp xếp
        </h4>
        <div class="space-y-3 text-sm text-gray-600">
            <p><strong>Lọc theo hạng thẻ:</strong> Sử dụng bộ lọc "Hạng thẻ thành viên" để xem khách hàng theo từng hạng thẻ cụ thể.</p>
            <p><strong>Sắp xếp:</strong> Nhấn vào tiêu đề cột để sắp xếp tăng/giảm dần theo trường đó.</p>
            <p><strong>Phân trang:</strong> Có thể chọn hiển thị 10, 25, 50 hoặc 100 bản ghi trên mỗi trang.</p>
        </div>
    </div>

    <!-- Data Info -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h4 class="text-base font-medium text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Thông tin dữ liệu
        </h4>
        <div class="space-y-3 text-sm text-gray-600">
            <p><strong>Nguồn dữ liệu:</strong> Tất cả dữ liệu được đồng bộ trực tiếp từ API MShopKeeper.</p>
            <p><strong>Cập nhật:</strong> Dữ liệu được cache trong 5 phút để tăng tốc độ tải trang.</p>
            <p><strong>Chỉ đọc:</strong> Không thể tạo mới, chỉnh sửa hoặc xóa khách hàng từ trang này.</p>
        </div>
    </div>

    <!-- Actions Guide -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h4 class="text-base font-medium text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
            </svg>
            Các thao tác có thể thực hiện
        </h4>
        <div class="space-y-3 text-sm text-gray-600">
            <p><strong>Xem chi tiết:</strong> Nhấn vào nút "Xem chi tiết" để xem thông tin đầy đủ của khách hàng.</p>
            <p><strong>Sao chép thông tin:</strong> Nhấn vào các trường có thể sao chép (mã KH, SĐT, email) để copy vào clipboard.</p>
            <p><strong>Xuất dữ liệu:</strong> Sử dụng các công cụ của trình duyệt để in hoặc lưu trang.</p>
        </div>
    </div>

    <!-- API Info -->
    <div class="bg-blue-50 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">API Endpoints được sử dụng</h3>
                <div class="mt-2 text-sm text-blue-700 space-y-1">
                    <p><strong>Danh sách khách hàng:</strong> POST /api/v1/customers/paging</p>
                    <p><strong>Tìm kiếm khách hàng:</strong> POST /api/v1/customers/customerbyinfo</p>
                    <p><strong>Hạng thẻ thành viên:</strong> POST /api/v1/customers/get-all-member-level</p>
                </div>
            </div>
        </div>
    </div>
</div>
