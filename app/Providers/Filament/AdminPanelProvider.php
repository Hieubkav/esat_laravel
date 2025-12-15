<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;

use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Support\Facades\Blade;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                \App\Filament\Admin\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                \App\Filament\Admin\Widgets\UserRoleWidget::class,
                \App\Filament\Admin\Widgets\PasswordInfoWidget::class,
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authGuard('web')
            ->authMiddleware([
                Authenticate::class,
            ])
            // Tối ưu hiệu suất
            ->spa()
            ->unsavedChangesAlerts()

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): string => Blade::render('
                    <style>
                        /* Tối ưu hiển thị RichEditor */
                        .fi-fo-rich-editor .ProseMirror img {
                            display: block !important;
                            max-width: 100%;
                            height: auto;
                            border-radius: 8px;
                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                            margin: 10px 0;
                        }
                    </style>
                ')
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn(): string => Blade::render('
                    <div class="flex items-center ml-4">
                        <a
                            href="{{ route(\'storeFront\') }}"
                            target="_blank"
                            title="Xem trang chủ website trong tab mới"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                            style="background-color: #dc2626 !important; color: white !important; border: 1px solid #b91c1c;"
                            onmouseover="this.style.backgroundColor=\'#b91c1c\' !important"
                            onmouseout="this.style.backgroundColor=\'#dc2626\' !important"
                        >
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span class="hidden sm:inline" style="color: white !important;">Xem Trang Chủ</span>
                            <span class="sm:hidden text-lg">👁️</span>
                        </a>
                    </div>
                ')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn(): string => Blade::render('
                    <script>
                        // Đơn giản - chỉ ẩn text chứa thông tin file
                        document.addEventListener("DOMContentLoaded", function() {
                            function hideFileInfo() {
                                const richEditors = document.querySelectorAll(".fi-fo-rich-editor .ProseMirror");
                                richEditors.forEach(editor => {
                                    const paragraphs = editor.querySelectorAll("p");
                                    paragraphs.forEach(p => {
                                        const text = p.textContent.trim();
                                        // Chỉ ẩn text chứa tên file + kích thước
                                        if (text.match(/\.(png|jpg|jpeg|gif|webp|svg)\s+[\d.]+\s*(KB|MB)/i)) {
                                            p.style.display = "none";
                                        }
                                    });
                                });
                            }

                            // Chạy sau khi DOM load và khi có thay đổi
                            hideFileInfo();
                            setTimeout(hideFileInfo, 1000);

                            const observer = new MutationObserver(() => setTimeout(hideFileInfo, 100));
                            observer.observe(document.body, { childList: true, subtree: true });
                        });
                    </script>
                ')
            )
            ->login()
            ->default();
    }
}
