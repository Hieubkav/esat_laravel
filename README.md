# Vũ Phúc - Website Doanh Nghiệp

Dự án website doanh nghiệp được xây dựng bằng Laravel 10 với Filament Admin Panel, tích hợp Livewire và Tailwind CSS.

## 🚀 Tính năng chính

- **Admin Panel**: Quản trị toàn diện với Filament
- **MShopKeeper Integration**: Kho hàng sản phẩm từ API MShopKeeper
- **Responsive Design**: Giao diện tối ưu cho mọi thiết bị
- **SEO Optimized**: Tối ưu hóa SEO tự động
- **Real-time Updates**: Cập nhật thời gian thực với Livewire
- **QR Code Integration**: Tích hợp mã QR cho nhân viên
- **Image Optimization**: Tự động chuyển đổi ảnh sang WebP

## 📋 Yêu cầu hệ thống

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Laravel 10.x

## 🛠️ Cài đặt

```bash
# Clone repository
git clone [repository-url]
cd vuphuc

# Cài đặt dependencies
composer install --ignore-platform-reqs
npm install

# Cấu hình môi trường
cp .env.example .env
php artisan key:generate

# Chạy migration
php artisan migrate --seed

# Build assets
npm run build

# Tối ưu hóa
php artisan icons:cache
php artisan filament:optimize
php artisan optimize

# Khởi động server
php artisan serve
```

## 📚 Tài liệu

Xem thêm tài liệu chi tiết trong thư mục `/docs`:

- [Hướng dẫn cài đặt](docs/installation.md)
- [Hướng dẫn phát triển](docs/development.md)
- [Hướng dẫn triển khai](docs/deployment.md)
- [Tài liệu API](docs/api.md)

## 🏗️ Cấu trúc dự án

```
vuphuc/
├── app/                    # Mã nguồn ứng dụng
├── docs/                   # Tài liệu dự án
├── public/                 # Assets công khai
├── resources/              # Views, CSS, JS
├── storage/                # File lưu trữ
└── tests/                  # Test cases
```

## 🛍️ MShopKeeper Integration

Hệ thống tích hợp với MShopKeeper API để hiển thị sản phẩm:

### Routes chính:
- `/kho-hang` - Trang chủ kho hàng + Tìm kiếm (parameter: `search`)
- `/kho-hang/loai/hang-hoa` - Hàng hoá
- `/kho-hang/loai/combo` - Combo sản phẩm
- `/kho-hang/loai/dich-vu` - Dịch vụ
- `/kho-hang/noi-bat` - Sản phẩm nổi bật

### Tính năng:
- Hiển thị sản phẩm theo loại (Hàng hoá, Combo, Dịch vụ)
- Tìm kiếm theo tên, mã sản phẩm, mã vạch
- Thông tin tồn kho chi tiết theo chi nhánh
- Sản phẩm nổi bật với ranking
- API thống kê real-time

Chi tiết: [docs/mshopkeeper-inventory-frontend.md](docs/mshopkeeper-inventory-frontend.md)

## 🤝 Đóng góp

Vui lòng đọc [CONTRIBUTING.md](docs/CONTRIBUTING.md) để biết thêm chi tiết.

## 📄 License

Dự án này được cấp phép theo MIT License.
