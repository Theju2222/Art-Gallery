<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use carbon\Carbon;

class CouponController extends Controller
{
    public function get_coupons()
    {
        $coupons = Coupon::all();
        
    
        if(!$coupons->isEmpty()){
            $couponArr = [];
            $now = Carbon::now()->timezone('Asia/kolkata');
            foreach ($coupons as $coupon) {
              
               
                if(Carbon::parse($coupon->expire_at)->isAfter($now)){
                   
                    $couponArr[] = $coupon;
                }
            }

            return response()->json(['coupons' => $couponArr, 'status' => 200], 200);
        }

        

        return response()->json(['message' => 'coupon not found', 'status' => 400], 400);
    }
}
