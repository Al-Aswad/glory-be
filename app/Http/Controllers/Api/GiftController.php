<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    public function getGifts(Request $request)
    {
        $gifts = \App\Models\Product::with('productStar')->get();
        return response()->json($gifts);
    }
}
