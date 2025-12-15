<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WebDesignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebDesignController extends Controller
{
    protected WebDesignService $webDesignService;

    public function __construct(WebDesignService $webDesignService)
    {
        $this->webDesignService = $webDesignService;
    }

    /**
     * Lấy tất cả cấu hình WebDesign
     */
    public function index(): JsonResponse
    {
        $sections = $this->webDesignService->getAllSections();

        return response()->json([
            'success' => true,
            'data' => $sections,
            'message' => 'WebDesign configuration retrieved successfully'
        ]);
    }

    /**
     * Lấy các sections hiển thị theo thứ tự
     */
    public function visible(): JsonResponse
    {
        $sections = $this->webDesignService->getVisibleSections();

        return response()->json([
            'success' => true,
            'data' => $sections,
            'message' => 'Visible sections retrieved successfully'
        ]);
    }

    /**
     * Lấy cấu hình của một section cụ thể
     */
    public function show(string $sectionKey): JsonResponse
    {
        $isVisible = $this->webDesignService->isVisible($sectionKey);
        $settings = $this->webDesignService->getSettings($sectionKey);
        $order = $this->webDesignService->getOrder($sectionKey);

        return response()->json([
            'success' => true,
            'data' => [
                'section_key' => $sectionKey,
                'is_visible' => $isVisible,
                'settings' => $settings,
                'order' => $order,
            ],
            'message' => "Section '{$sectionKey}' configuration retrieved successfully"
        ]);
    }

    /**
     * Clear cache WebDesign
     */
    public function clearCache(): JsonResponse
    {
        $this->webDesignService->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'WebDesign cache cleared successfully'
        ]);
    }

    /**
     * Export cấu hình WebDesign
     */
    public function export(): JsonResponse
    {
        $sections = $this->webDesignService->getAllSections();

        $exportData = [
            'version' => '1.0',
            'exported_at' => now()->toISOString(),
            'sections' => $sections,
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData,
            'message' => 'WebDesign configuration exported successfully'
        ]);
    }

    /**
     * Reset về cấu hình mặc định
     */
    public function reset(): JsonResponse
    {
        $this->webDesignService->resetToDefault();

        return response()->json([
            'success' => true,
            'message' => 'WebDesign configuration reset to default successfully'
        ]);
    }
}
