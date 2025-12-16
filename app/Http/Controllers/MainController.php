<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cat;
use App\Models\Product;

class MainController extends Controller
{
    public function storeFront()
    {
        $components = \App\Models\HomeComponent::where('active', true)
            ->orderBy('order')
            ->get();

        return view('shop.storeFront', compact('components'));
    }
}
