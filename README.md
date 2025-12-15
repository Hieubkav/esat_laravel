# CÔNG TY TNHH ESAT - Website Doanh Nghiệp

Dự án website doanh nghiệp ESAT - Chuyên cung cấp thiết bị điện tử, được xây dựng bằng Laravel 10 với Filament Admin Panel, tích hợp Livewire và Tailwind CSS.

## 🚀 Tính năng chính

- **Admin Panel**: Quản trị toàn diện với Filament
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
cd esat_laravel

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

## 🏗️ Cấu trúc dự án

```
esat_laravel/
├── app/                    # Mã nguồn ứng dụng
├── public/                 # Assets công khai
├── resources/              # Views, CSS, JS
├── storage/                # File lưu trữ
└── tests/                  # Test cases
```

## 📄 License

Dự án này được cấp phép theo MIT License.
