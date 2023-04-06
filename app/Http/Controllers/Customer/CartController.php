<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Size;
use App\Models\Style;
use App\Models\Medium;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Rating;
use App\Address;
use App\Offer;
use App\Wishlist;
use carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function cartList()
    {
        $cartItems = $this->getcartList();
        
        if($cartItems){
            return response()->json(['cart_items'  => $cartItems, 'total' => $this->getCartTotal(), 'status' => 200], 200);
        }
       
        return response()->json(['message'  => 'no item found', 'status' => 400], 400);
       
    }

    public function getcartList()
    {
        $cart = Cart::where(['user_id' => Auth::user()->id, 'expired_at' => null])->first();

        if($cart == null){
            return false;
        }
        
        $cartItems = CartItem::where('cart_id', $cart->id)->get();
        if($cartItems->isEmpty()){
            return false;
        }
        foreach ($cartItems as $item) {
            $product = Product::where('product_id', $item->item_id)->first();
            if($product !== null){
                $size = Size::where('id', $product->size)->first();
                $style = Style::where('id', $product->style)->first();
                $medium = Medium::where('id', $product->medium)->first();
                $product->size = $size->size;
                $product->medium = $medium->medium;
                $product->style = $style->style;
                $imagesArr = json_decode($product->images);
                $product->images = env('APP_URL').'/storage/'.$imagesArr[0];
                $item->product = $product;
            }

        }
        return $cartItems;
      
    }


    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            'qty' => 'required',
            
        ]);

        $user = Auth::user();

        $product = Product::where(['product_id' => $request->product_id])->first();
        if($product == null){
            return response()->json(['message' => 'product not found', 'status' => 400], 400); 
        }
        
        $pre_cart = Cart::where(['user_id' => $user->id, 'expired_at' => null])->first();

        if($pre_cart == null){
           
        
       
         

            $cart = new Cart();
            $cart->user_id =  $user->id; 

            if($cart->save()){
                $new_cart_item = new CartItem();
                $new_cart_item->cart_id = $cart->id;
                $new_cart_item->item_id = $request->product_id;
                $new_cart_item->qty =  $request->qty;
              

                if($new_cart_item->save()){
                    $cartlist = $this->getcartList();
                    return response()->json(['message' => 'cart added successfully', 'status' => 200, 'cart_items' => $cartlist, 'total' => $this->getCartTotal() ], 200);
                }

              
                return response()->json(['message' => 'cart can not be added ', 'status' => 400], 400);

               
            }

            return response()->json(['message' => 'cart can not be added', 'status' => 400], 400);
        
        }
        
        $cartItem = CartItem::where(['item_id' => $product->product_id, 'cart_id' => $pre_cart->id])->first();
        
        if($cartItem !== null){

          
            $cartItem->qty += $request->qty;
         
            if($cartItem->save()){
                $cart = $this->getcartList();
                return response()->json(['message' => 'Item qunatity updated', 'status' => 200, 'cart_items' => $cart, 'total' => $this->getCartTotal()], 200);
            }
            return response()->json(['message' => 'Item quantity can not be updated ', 'status' => 400], 400);  

        }

        $new_cart_item = new CartItem();
        $new_cart_item->cart_id = $pre_cart->id;
        $new_cart_item->item_id = $request->product_id;
        $new_cart_item->qty = $request->qty;
     
        if($new_cart_item->save()){
            $cart = $this->getcartList();
            return response()->json(['message' => 'item added to cart', 'status' => 200, 'cart_items' => $cart, 'total' => $this->getCartTotal()], 200);
        } 
       

        return response()->json(['message' => 'item can not be added to cart', 'status' => 400], 400);
        
        
    }

  

    public function removeCart(Request $request)
    {
        $request->validate([
           
            'product_id' => 'required|string',
        ]);

        $user = Auth::user();

        $pre_cart = Cart::where(['user_id' => $user->id, 'expired_at' => null])->first();
        
        if($pre_cart == null){
            return response()->json(['message' => 'cart not found', 'status' => 400], 400);  
        }

        $cartItem = CartItem::where(['item_id' => $request->product_id, 'cart_id' => $pre_cart->id])->first();
        
        if($cartItem->delete()){
            $cart = $this->getcartList();
            return response()->json(['message' => 'Item removed from cart', 'status' => 200, 'cart_items' => $cart, 'total' => $this->getCartTotal()], 200);  

        }

        return response()->json(['message' => 'Item is not in your cart ', 'status' => 400], 400);  


    }

    public function clearAllCart()
    {
        $user = Auth::user();

        $pre_cart = Cart::where(['user_id' => $user->id, 'expired_at' => null])->first();
         
        if($pre_cart->delete()){

            return response()->json(['message' => 'cart has been deleted', 'status' => 200], 200);  

        }
        return response()->json(['message' => 'cart can not be deleted', 'status' => 400], 400);
    }

    public function getCartTotal()
    {
        $total = 0;
        $user = Auth::user();
        $pre_cart = Cart::where(['user_id' => $user->id, 'expired_at' => null])->first();
         
        if($pre_cart !== null){

            $cartItems = CartItem::where('cart_id', $pre_cart->id)->get();
          
            foreach ($cartItems as $item) {
               $product = Product::where('product_id', $item->item_id)->first();
               
               if($product == null){
                   $total += 0; 
               }else{

                
               
                

               
                $price = $product->price*$item->qty; 

                $total += $price;
               }
              
            } 

        }
        return $total;
    }
    public function getTotal()
    {
        $total = 0;
        $user = Auth::user();
        $pre_cart = Cart::where(['user_id' => $user->id, 'expired_at' => null])->first();
         
        if($pre_cart !== null){

            $cartItems = CartItem::where('cart_id', $pre_cart->id)->get();
          
            foreach ($cartItems as $item) {
               $product = Product::where('product_id', $item->item_id)->first();
               
               if($product == null){
                   $total += 0; 
               }else{

                $discount = 0;
                $offer = Offer::where('product_id', $product->product_id)->first();
               
                if($offer !== null){
                    if($offer->off_type == 1){

                        $discount = $offer->off_discount;

                    }elseif($offer->off_type == 2){

                        $discount = ($offer->off_discount*$product->price)/100;

                    }elseif($offer->off_type == 3){

                        $min=1;
                        $max=$offer->off_discount;
                        $discount = mt_rand($min,$max);

                    }else{

                        $min=1;
                        $max=$offer->off_discount;
                        $discount = (mt_rand($min,$max)*$product_price)/100;

                    }
                
                }

               
                $product->price -= $discount; 

                $total += $product->price;
               }
              
            } 

        }
        return $total;
    }



    /**
     * add order
     * @param Request $request
     * @return User
     */

    public function create_order(Request $request)
    {

        $request->validate([                
            'coupon_id' => 'nullable',
            'address_id' => 'required'
            
            
        ]);
    
    
        $user = Auth::user();
        $cart = Cart::where(['user_id' => $user->id, 'expired_at' => null])->first();
        
        $cartItems = CartItem::where('cart_id', $cart->id)->get();
        $products = []; 
        foreach ($cartItems as $item) {
            $product = Product::where('product_id', $item->item_id)->first();
            if($item->qty < $product->qty ){
                return response()->json([
                    'status' => 400,
                    'message' => $product->name.'does not have enough quantity'
                ]);
            }
            $product->inventory -= $item->qty;
            $prdoucts[] = $product;
        }

        $order = new Order();
        
        $order->order_id = 'ORDER'.time();
        $address = Address::where('id', $request->address_id)->first();
        $order->customer_name = $address->name;
        $order->customer_mobile = $address->mobile;

        $order->address = $address->street.', '.$address->landmark.', '.$address->city.', '.$address->state.', '.$address->country.', '.$address->pin_code;
        $total = $this->getTotal();
        $discount = 0;
        if($request->coupon_id !== null){

           
            $order->coupon_id = $request->coupon_id; 
            $coupon = Coupon::where('id', $request->coupon_id)->first();
            if($coupon !== null){

                if(Carbon::now()->isAfter($coupon->expire_at)){
                    return response()->json([
                        'status' => 400,
                        'message' => 'coupon has been expired'
                    ], 400);
                }
                if($coupon->type == 1){
                    $discount = $coupon->discount;
                }elseif($coupon->type == 2){
                    $discount = ($coupon->discount*$total)/100;
                }elseif($coupon->type == 3){
                    $min=1;
                    $max=$coupon->discount;
                    $discount = mt_rand($min,$max);
                }else{
                    $min=1;
                    $max=$coupon->discount;
                    $discount = (mt_rand($min,$max)*$total)/100;
                }
            }

        }

        $order->amount = $total - $discount;
        $order->status = 3; //3 = pending, 1 = Accepted, 2 = completed, 0 = Canceled
        $order->payment_status = 0; //0 = not done, 1 = done,
        $order->user_id = $user->id;
    
        $date = Carbon::now()->format('d');
        $month = Carbon::now()->format('F');
        $year = Carbon::now()->format('Y');

        $order_date = $date.' '.$month.' '.$year;

        $order->order_date = $order_date;
        $order->cart_id = $cart->id;

        if($order->save()){
            
            $cart->expired_at = Carbon::now();
            $cart->save();

            foreach ($products as $product) {
                $product->save();
            }

            return response()->json([
            'status' => 200,
            'message' => 'order has been saved',
            'order' => $order,
        
            ], 200);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'order could not be saved'
            ], 400);
        }

    
    }




    /**
     * add order by checkout
     * @param Request $request
     * @return User
     */

    public function create_order_by_chekout(Request $request)
    {

        $request->validate([                
            'coupon_id' => 'nullable',
            'address_id' => 'required',
            'qty' => 'required',
            'product_id' => 'required'
        ]);
    
    
        $user = Auth::user();
       
        

        $order = new Order();
        
        $order->order_id = 'ORDER'.time();
        $address = Address::where('id', $request->address_id)->first();
        $order->customer_name = $address->name;
        $order->customer_mobile = $address->mobile;

        $order->address = $address->street.', '.$address->landmark.', '.$address->city.', '.$address->state.', '.$address->country.', '.$address->pin_code;
        $product = Product::where('product_id', $request->product_id)->first();
        $order->product_id = $request->product_id; 
        $order->qty = $request->qty; 
        $total = $product->price*$request->qty;

        $discount = 0;
        if($request->coupon_id !== null){

           
            $order->coupon_id = $request->coupon_id; 
            $coupon = Coupon::where('id', $request->coupon_id)->first();
            if($coupon !== null){

                if(Carbon::now()->isAfter($coupon->expire_at)){
                    return response()->json([
                        'status' => 400,
                        'message' => 'coupon has been expired'
                    ], 400);
                }
                if($coupon->type == 1){
                    $discount = $coupon->discount;
                }elseif($coupon->type == 2){
                    $discount = ($coupon->discount*$total)/100;
                }elseif($coupon->type == 3){
                    $min=1;
                    $max=$coupon->discount;
                    $discount = mt_rand($min,$max);
                }else{
                    $min=1;
                    $max=$coupon->discount;
                    $discount = (mt_rand($min,$max)*$total)/100;
                }
            }

        }

        $order->amount = $total - $discount;
        $order->status = 3; //3 = pending, 1 = Accepted, 2 = completed, 0 = Canceled
        $order->payment_status = 0; //0 = not done, 1 = done,
        $order->user_id = $user->id;
    
        $date = Carbon::now()->format('d');
        $month = Carbon::now()->format('F');
        $year = Carbon::now()->format('Y');

        $order_date = $date.' '.$month.' '.$year;

        $order->order_date = $order_date;
        

        if($order->save()){
            
            

           $product->inventory -= $request->qty; 
           $product->save();
            

            return response()->json([
            'status' => 200,
            'message' => 'order has been saved',
            'order' => $order,
        
            ], 200);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'order could not be saved'
            ], 400);
        }

    
    }



    /**
     * get orders
     * @param Request $request
     * @return User
     */

    public function get_orders(Request $request)
    {

       
    
    
        $user = Auth::user();
        $orders = Order::where(['user_id' => $user->id])->get();
        if($orders->isEmpty()){
            return response()->json([
                'status' => 400,
                'message' => 'no order found'
            ]);
        }
        foreach ($orders as $order) {
            if($order->cart_id == 0){
                $product = Product::where('product_id', $order->product_id)->get();
                $products = [];
                $products[] = $product;
                
            }else{
                $cartItems = CartItem::where('cart_id', $order->cart_id)->get();
                $products = [];
                foreach ($cartItems as $item) {
                    $product = Product::where('product_id', $item->item_id)->first();
                    $products[] = $product;
                
                }
            }

            $order->products = $products;
        }
        return response()->json([
            'status' => 200,
          
            'orders' => $orders,
        
        ], 200);

       

       

    
    }

    



    /**
     * get order
     * @param Request $request
     * @return User
     */

    public function get_order_detail(Request $request)
    {

       
        $request->validate([
           'id' => 'required'
        ]);

    
        $user = Auth::user();
        $order = Order::where(['user_id' => $user->id, 'id'=>$request->id])->first();
        if($order == null){
            return response()->json([
                'status' => 400,
                'message' => 'order not found'
            ]);
        }
       
        if($order->cart_id == 0){
            $products = [];
            $product = Product::where('product_id', $order->product_id)->first();
            $ratings = $this->get_all_reviews($product->product_id);
            if($ratings){
                $product->reviews = $ratings;
            }else{
                $ratings = [];
                $product->reviews = $ratings;
            }
            $size = Size::where('id', $product->size)->first();
            $style = Style::where('id', $product->style)->first();
            $medium = Medium::where('id', $product->medium)->first();
            $product->size = $size->size;
            $product->medium = $medium->medium;
            $product->style = $style->style;  
            
            $imagesArr = json_decode($product->images);
            $images = [];
            foreach ($imagesArr as $image) {
                $images[] = env('APP_URL').'/storage/'.$image;
            }
            $product->images = $images;
            $product->qty = $order->qty;

            $products[] = $product;
        }else{
            $cartItems = CartItem::where('cart_id', $order->cart_id)->get();
            $products = [];
            foreach ($cartItems as $item) {


                $product = Product::where('product_id', $item->item_id)->first();
                $ratings = $this->get_all_reviews($product->product_id);
                if($ratings){
                    $product->reviews = $ratings;
                }else{
                    $ratings = [];
                    $product->reviews = $ratings;
                }
                $size = Size::where('id', $product->size)->first();
                $style = Style::where('id', $product->style)->first();
                $medium = Medium::where('id', $product->medium)->first();
                $product->size = $size->size;
                $product->medium = $medium->medium;
                $product->style = $style->style;  
                
                $imagesArr = json_decode($product->images);
                $images = [];
                foreach ($imagesArr as $image) {
                    $images[] = env('APP_URL').'/storage/'.$image;
                }
                $product->images = $images;
                $product->qty = $item->qty;
                $products[] = $product;
                
            }
        }
        

        $order->products = $products;
      
        return response()->json([
            'status' => 200,
          
            'order' => $order,
        
        ], 200);

       

       

    
    }



    private function get_all_reviews($product_id)
    {
        
        $user = Auth::user();
        $ratings = Rating::where(['product_id' => $product_id, 'customer_id' => $user->id])->get();

        if($ratings->isEmpty()){
            return 0;
        }

        foreach ($ratings as $rating) {
            
            $rating->customer_name = $user->name;
            $rating->avatar = env('APP_URL').'/storage/'.$user->avatar;
        }

        return $ratings;
    }



    /**
     * cancel order
     * @param Request $request
     * @return User
     */

    public function cancel_order(Request $request)
    {

       
        $request->validate([
           'id' => 'required'
        ]);

    
        $user = Auth::user();
        $order = Order::where(['user_id' => $user->id, 'id'=>$request->id])->first();
        if($order == null){
            return response()->json([
                'status' => 400,
                'message' => 'order not found'
            ]);
        }

        $order->status = 0;
       
        if($order->save()){
            return response()->json([
                'status' => 200,
              
                'message' => 'order has been canceled',
            
            ], 200);
        }
        
        return response()->json([
            'status' => 400,
          
            'message' => 'order can not be canceled',
        
        ], 400);
       

       

    
    }



    /**
     * update order
     * @param Request $request
     * @return User
     */

    public function update_order(Request $request)
    {

       
        $request->validate([
           'id' => 'required',
           'payment_status' => 'required',
           'payment_id' => 'nullable|string'
        ]);

    
        $user = Auth::user();
        $order = Order::where(['user_id' => $user->id, 'id' => $request->id])->first();
        if($order == null){
            return response()->json([
                'status' => 400,
                'message' => 'order not found'
            ]);
        }
        $order->payment_status = $request->payment_status;

        if($request->payment_id !== null){
            $order->payment_id = $request->payment_id;
        }
        if($order->save()){
            return response()->json([
                'status' => 200,
              
                'message' => 'payment has been updated',
            
            ], 200);
        }
        
        return response()->json([
            'status' => 400,
          
            'message' => 'payment status can not be updated',
        
        ], 400);
       

       

    
    }


    public function add_to_wishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            
        ]);

        $product = Product::where(['product_id' => $request->product_id, 'status' => 1])->first();

        if($product == null){
            return response()->json(['message' => 'product not found', 'status' => 400], 400);
        }

        $user = Auth::user();
   
        $pre_wishlist = Wishlist::where(['user_id' => $user->id, 'product_id' => $request->product_id ])->first();
        
        if($pre_wishlist !== null){
            return response()->json(['message' => 'already in wishlist', 'status' => 400], 400);
        }

        $wishlist = new Wishlist();
        $wishlist->user_id = $user->id;
        $wishlist->product_id = $request->product_id;

        if($wishlist->save()){
            return response()->json(['message' => 'added to wishlist', 'status' => 200], 200);
        }
    }

    public function get_wishlist()
    {
        $user = Auth::user();
        $wishlist = Wishlist::where('user_id', $user->id)->get();
        
        if($wishlist->isEmpty()){
            return response()->json(['message' => 'no item in wishlist found', 'status' => 400], 400);
        }

        foreach ($wishlist as $item) {
            $product = Product::where('product_id', $item->product_id)->first();
            if($product !== null){
                $size = Size::where('id', $product->size)->first();
                $style = Style::where('id', $product->style)->first();
                $medium = Medium::where('id', $product->medium)->first();
                $product->size = $size->size;
                $product->medium = $medium->medium;
                $product->style = $style->style;
                $imagesArr = json_decode($product->images);
                $product->images = env('APP_URL').'/storage/'.$imagesArr[0];
                $item->product = $product;
            }
        }

        return response()->json(['wishlist' => $wishlist, 'status' => 200], 200);
    }
}
