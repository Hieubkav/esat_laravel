<div class="space-y-6">
    <!-- Giải thích tổng quan -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Đơn đặt hàng vs Hóa đơn bán hàng</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p><strong>Đơn đặt hàng (Orders):</strong> Yêu cầu mua hàng từ khách hàng, chưa thanh toán</p>
                    <p><strong>Hóa đơn (Invoices):</strong> Chứng từ bán hàng đã hoàn tất, đã thanh toán</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quy trình xử lý -->
    <div>
        <h4 class="text-lg font-semibold text-gray-900 mb-4">🔄 Quy trình xử lý đơn đặt hàng</h4>
        
        <div class="space-y-4">
            <!-- Bước 1 -->
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-500 text-white rounded-full text-sm font-bold">1</div>
                </div>
                <div class="ml-4">
                    <h5 class="font-medium text-gray-900">Khách hàng đặt hàng</h5>
                    <p class="text-sm text-gray-600">Khách hàng sử dụng Quick Order Modal trên website để đặt hàng</p>
                    <div class="mt-1 text-xs text-blue-600">→ Tạo đơn đặt hàng với mã DT000xxx</div>
                </div>
            </div>

            <!-- Bước 2 -->
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-yellow-500 text-white rounded-full text-sm font-bold">2</div>
                </div>
                <div class="ml-4">
                    <h5 class="font-medium text-gray-900">Thu ngân nhận đơn</h5>
                    <p class="text-sm text-gray-600">Đơn hàng xuất hiện trong danh sách này và trên phần mềm MShopKeeper PC</p>
                    <div class="mt-1 text-xs text-yellow-600">→ Trạng thái: Chờ xử lý (Pending)</div>
                </div>
            </div>

            <!-- Bước 3 -->
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-orange-500 text-white rounded-full text-sm font-bold">3</div>
                </div>
                <div class="ml-4">
                    <h5 class="font-medium text-gray-900">Xử lý trên MShopKeeper PC</h5>
                    <p class="text-sm text-gray-600">Thu ngân mở phần mềm MShopKeeper PC và xử lý đơn hàng:</p>
                    <ul class="mt-2 text-xs text-gray-600 list-disc list-inside space-y-1">
                        <li>Kiểm tra thông tin khách hàng</li>
                        <li>Xác nhận sản phẩm có sẵn</li>
                        <li>Xử lý thanh toán</li>
                        <li>Chuẩn bị hàng giao</li>
                    </ul>
                </div>
            </div>

            <!-- Bước 4 -->
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-green-500 text-white rounded-full text-sm font-bold">4</div>
                </div>
                <div class="ml-4">
                    <h5 class="font-medium text-gray-900">Tạo hóa đơn</h5>
                    <p class="text-sm text-gray-600">Sau khi xử lý xong, hệ thống tự động tạo hóa đơn bán hàng</p>
                    <div class="mt-1 text-xs text-green-600">→ Hóa đơn sẽ xuất hiện trong "Hóa đơn MShopKeeper"</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lưu ý quan trọng -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Lưu ý quan trọng</h3>
                <div class="mt-2 text-sm text-yellow-700 space-y-1">
                    <p>• <strong>Đơn đặt hàng</strong> chỉ là yêu cầu mua hàng, chưa phải giao dịch hoàn tất</p>
                    <p>• Thu ngân cần xử lý trên <strong>phần mềm MShopKeeper PC</strong> để hoàn tất</p>
                    <p>• Chỉ sau khi xử lý xong mới có <strong>hóa đơn bán hàng</strong> chính thức</p>
                    <p>• Hóa đơn sẽ tự động sync về website trong mục "Hóa đơn MShopKeeper"</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Hướng dẫn nhanh -->
    <div>
        <h4 class="text-lg font-semibold text-gray-900 mb-3">⚡ Hướng dẫn nhanh cho thu ngân</h4>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                <li>Kiểm tra đơn đặt hàng mới trong danh sách này</li>
                <li>Mở phần mềm MShopKeeper PC trên máy tính</li>
                <li>Tìm đơn hàng theo mã (VD: DT000025)</li>
                <li>Xử lý đơn hàng: kiểm tra hàng, thanh toán, giao hàng</li>
                <li>Hóa đơn sẽ tự động xuất hiện trong "Hóa đơn MShopKeeper"</li>
            </ol>
        </div>
    </div>

    <!-- Liên hệ hỗ trợ -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <h4 class="font-medium text-gray-900 mb-2">📞 Cần hỗ trợ?</h4>
        <p class="text-sm text-gray-600">
            Nếu gặp vấn đề với việc xử lý đơn hàng hoặc đồng bộ dữ liệu, 
            vui lòng liên hệ bộ phận IT để được hỗ trợ.
        </p>
    </div>
</div>
