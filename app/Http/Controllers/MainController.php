<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cat;
use App\Models\Product;

class MainController extends Controller
{
    public function storeFront()
    {
        return view('shop.storeFront');
    }

}
