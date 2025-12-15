<x-filament-panels::page class="fi-dashboard-page">
    <!-- Executive Header -->
    <div class="executive-header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold mb-2 text-white dark:text-gray-100">📊 Dashboard Điều Hành</h1>
                <p class="text-blue-100 dark:text-blue-200">Tổng quan hoạt động kinh doanh realtime</p>
            </div>
            <div class="text-right flex-shrink-0">
                <div class="text-sm text-blue-100 dark:text-blue-200 mb-1">Cập nhật lần cuối</div>
                <div class="text-lg font-semibold text-white dark:text-gray-100" id="last-update-time">{{ now()->format('H:i:s d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        🔄 Auto-refresh mỗi 5 giây |
                        📊 Dữ liệu realtime |
                        🧪 Công cụ test tracking
                    </span>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Sử dụng widget "Bảng Điều Khiển Tracking" bên dưới để reset dữ liệu
                </div>
            </div>
        </div>
    </div>



    @if (method_exists($this, 'filtersForm'))
        <div class="mb-6">
            {{ $this->filtersForm }}
        </div>
    @endif

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                ...$this->getWidgetData(),
            ]
        "
        :widgets="$this->getVisibleWidgets()"
    />

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Hiển thị thông báo khi dashboard được tải
                if (typeof window.filamentData !== 'undefined') {
                    console.log('Dashboard realtime đã được kích hoạt');
                }

                // Thêm hiệu ứng loading khi widget refresh
                document.addEventListener('livewire:load', function () {
                    Livewire.hook('message.sent', (message, component) => {
                        if (component.fingerprint.name.includes('widget')) {
                            // Thêm class loading
                            const element = component.el;
                            if (element) {
                                element.style.opacity = '0.7';
                                element.style.transition = 'opacity 0.3s ease';
                            }
                        }
                    });

                    Livewire.hook('message.processed', (message, component) => {
                        if (component.fingerprint.name.includes('widget')) {
                            // Xóa class loading
                            const element = component.el;
                            if (element) {
                                element.style.opacity = '1';
                            }
                        }
                    });
                });

                // Auto refresh toàn bộ dashboard mỗi 60 giây
                setInterval(function() {
                    if (typeof Livewire !== 'undefined') {
                        console.log('Đang cập nhật dashboard...');
                        // Refresh tất cả widgets
                        Livewire.all().forEach(function(component) {
                            if (component.fingerprint.name.includes('widget')) {
                                component.call('$refresh');
                            }
                        });
                    }
                }, 60000); // 60 seconds

                // Hiển thị thời gian cập nhật cuối
                function updateLastRefreshTime() {
                    const now = new Date();
                    const timeString = now.toLocaleTimeString('vi-VN');

                    // Tìm hoặc tạo element hiển thị thời gian
                    let timeElement = document.getElementById('last-refresh-time');
                    if (!timeElement) {
                        timeElement = document.createElement('div');
                        timeElement.id = 'last-refresh-time';
                        timeElement.className = 'text-sm text-gray-500 text-right mb-4';
                        timeElement.innerHTML = `<i class="fas fa-clock"></i> Cập nhật lần cuối: ${timeString}`;

                        const dashboardContent = document.querySelector('.fi-dashboard-page');
                        if (dashboardContent) {
                            dashboardContent.insertBefore(timeElement, dashboardContent.firstChild);
                        }
                    } else {
                        timeElement.innerHTML = `<i class="fas fa-clock"></i> Cập nhật lần cuối: ${timeString}`;
                    }
                }

                // Function cập nhật thời gian header
                function updateHeaderTime() {
                    const now = new Date();
                    const timeString = now.toLocaleString('vi-VN');
                    const headerTimeElement = document.getElementById('last-update-time');
                    if (headerTimeElement) {
                        headerTimeElement.textContent = timeString;
                    }
                }

                // Cập nhật thời gian ngay khi load
                updateLastRefreshTime();
                updateHeaderTime();

                // Cập nhật thời gian mỗi khi có refresh
                document.addEventListener('livewire:load', function () {
                    Livewire.hook('message.processed', function() {
                        updateLastRefreshTime();
                        updateHeaderTime();
                    });
                });

                // Cập nhật thời gian header mỗi giây
                setInterval(updateHeaderTime, 1000);

                // Handle filter changes - force page reload for immediate update
                document.addEventListener('livewire:init', () => {
                    Livewire.on('filtersUpdated', () => {
                        console.log('Filter updated, reloading page...');
                        // Force page reload to ensure all widgets get new filter values
                        setTimeout(() => {
                            window.location.reload();
                        }, 100);
                    });
                });

                // Listen for select changes in filter form
                document.addEventListener('change', (event) => {
                    if (event.target.name === 'data.period') {
                        console.log('Filter select changed, reloading...');
                        setTimeout(() => {
                            window.location.reload();
                        }, 200);
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            /* Executive Dashboard Styles - Dark/Light Mode Compatible */
            .fi-dashboard-page {
                @apply bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800;
                min-height: 100vh;
                transition: background 0.3s ease;
            }

            /* KPI Cards - Executive Level with Theme Support */
            .executive-kpi-primary {
                @apply bg-gradient-to-br from-emerald-600 to-emerald-500 dark:from-emerald-700 dark:to-emerald-600;
                @apply text-white dark:text-gray-100;
                @apply border-emerald-500 dark:border-emerald-600;
                @apply shadow-lg shadow-emerald-500/25 dark:shadow-emerald-600/25;
                border: 1px solid !important;
                transition: all 0.3s ease;
            }

            .executive-kpi-primary:hover {
                @apply shadow-xl shadow-emerald-500/30 dark:shadow-emerald-600/30;
                transform: translateY(-2px);
            }

            .executive-kpi-primary .fi-wi-stats-overview-stat-value {
                @apply text-white dark:text-gray-100 !important;
                font-size: 2rem !important;
                font-weight: 800 !important;
            }

            .executive-kpi-primary .fi-wi-stats-overview-stat-description {
                @apply text-emerald-50 dark:text-emerald-100 !important;
                opacity: 0.9;
            }

            /* Stats Overview - Theme Compatible */
            .fi-wi-stats-overview-stat {
                @apply bg-white dark:bg-gray-800;
                @apply border border-gray-200 dark:border-gray-700;
                @apply shadow-sm dark:shadow-gray-900/20;
                @apply rounded-xl;
                transition: all 0.3s ease;
            }

            .fi-wi-stats-overview-stat:hover {
                @apply shadow-lg dark:shadow-gray-900/40;
                @apply border-blue-300 dark:border-blue-600;
                @apply bg-gray-50 dark:bg-gray-750;
                transform: translateY(-4px);
            }

            /* Charts - Theme Compatible */
            .fi-wi-chart {
                @apply bg-white dark:bg-gray-800;
                @apply border border-gray-200 dark:border-gray-700;
                @apply shadow-sm dark:shadow-gray-900/20;
                @apply rounded-xl;
                transition: all 0.3s ease;
            }

            .fi-wi-chart:hover {
                @apply shadow-md dark:shadow-gray-900/30;
                @apply border-gray-300 dark:border-gray-600;
                transform: translateY(-2px);
            }

            /* Tables - Theme Compatible */
            .fi-wi-table {
                @apply bg-white dark:bg-gray-800;
                @apply border border-gray-200 dark:border-gray-700;
                @apply shadow-sm dark:shadow-gray-900/20;
                @apply rounded-xl overflow-hidden;
                transition: all 0.3s ease;
            }

            .fi-wi-table:hover {
                @apply shadow-md dark:shadow-gray-900/30;
            }

            /* Widgets - Theme Compatible */
            .fi-wi {
                @apply bg-white dark:bg-gray-800;
                @apply border border-gray-200 dark:border-gray-700;
                @apply shadow-sm dark:shadow-gray-900/20;
                @apply rounded-xl;
                transition: all 0.3s ease;
            }

            .fi-wi:hover {
                @apply shadow-md dark:shadow-gray-900/30;
                @apply border-gray-300 dark:border-gray-600;
            }

            /* Section Headers - Theme Compatible */
            .fi-section-header-heading {
                @apply text-lg font-semibold text-gray-900 dark:text-gray-100;
            }

            /* Loading Animation */
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }

            .loading {
                animation: pulse 1.5s ease-in-out infinite;
            }

            /* Responsive Improvements */
            @media (max-width: 768px) {
                .fi-wi-stats-overview-stat {
                    @apply mb-4;
                }

                .executive-kpi-primary .fi-wi-stats-overview-stat-value {
                    font-size: 1.5rem !important;
                }

                .executive-header {
                    @apply p-4;
                }

                .executive-header h1 {
                    @apply text-xl;
                }
            }

            /* Alert Animations */
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            .alert-item {
                animation: slideIn 0.3s ease-out;
            }

            /* Executive Header - Theme Compatible */
            .executive-header {
                @apply bg-gradient-to-br from-blue-600 to-blue-500 dark:from-blue-700 dark:to-blue-600;
                @apply text-white dark:text-gray-100;
                @apply shadow-lg shadow-blue-500/25 dark:shadow-blue-600/25;
                @apply rounded-xl p-6 mb-8;
                transition: all 0.3s ease;
            }

            .executive-header:hover {
                @apply shadow-xl shadow-blue-500/30 dark:shadow-blue-600/30;
            }

            /* Status Indicators - Theme Compatible */
            .status-indicator {
                @apply inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium;
                transition: all 0.2s ease;
            }

            .status-success {
                @apply bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400;
            }

            .status-warning {
                @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400;
            }

            .status-danger {
                @apply bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400;
            }

            .status-info {
                @apply bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400;
            }

            /* Additional Theme Enhancements */
            .fi-section {
                @apply bg-white dark:bg-gray-800;
                @apply border border-gray-200 dark:border-gray-700;
                transition: all 0.3s ease;
            }

            /* Form Styling */
            .fi-fo-field-wrp {
                @apply bg-white dark:bg-gray-800;
            }

            /* Ensure proper text contrast */
            .fi-dashboard-page * {
                transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
            }
        </style>
    @endpush
</x-filament-panels::page>
