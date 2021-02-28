<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Currency;
use App\Models\Coupon;
use App\Models\Generalsetting;
use Auth;
use Session;

class TestController extends Controller
{

    public function test1(): bool
    {
        return false;
    }
}
