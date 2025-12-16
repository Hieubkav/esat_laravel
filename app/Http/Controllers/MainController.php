<?php

namespace App\Http\Controllers;

use App\Models\HomeComponent;
use App\Services\HomeComponentDataService;

class MainController extends Controller
{
    public function __construct(
        protected HomeComponentDataService $componentDataService
    ) {}

    public function storeFront()
    {
        $components = HomeComponent::where('active', true)
            ->orderBy('order')
            ->get();

        // Pre-load data cho từng component (tránh query trong Blade)
        $componentData = [];
        foreach ($components as $component) {
            $componentData[$component->id] = $this->componentDataService->loadComponentData(
                $component->type,
                $component->config ?? []
            );
        }

        return view('shop.storeFront', compact('components', 'componentData'));
    }
}
