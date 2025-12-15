<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EcomerceController extends Controller
{
    public function index()
    {
        return view('shop.ecomerce');
    }
}
