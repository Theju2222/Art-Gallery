<?php



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Customer\PreAuthController;

use App\Http\Controllers\Customer\AuthController;

use App\Http\Controllers\Customer\CartController;

use App\Http\Controllers\Customer\CouponController;

use App\Http\Controllers\PublicController;





/*

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

|

| Here is where you can register API routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| is assigned the "api" middleware group. Enjoy building your API!

|

*/



//customer routes



Route::group(['prefix' => 'user'], function() {



    //customer Pre Authentication Routes

    Route::controller(PublicController::class)->group(function () {

        Route::get('/get_product', 'get_product');

        Route::get('/get_all_products', 'get_all_products');

        Route::get('/get_all_categories', 'get_all_categories');

        Route::get('/get_all_banners', 'get_all_banners');

        Route::post('/filter_products', 'filter');

        Route::get('/get_all_reviews', 'get_all_reviews');
        
        Route::get('/get_all_offers', 'get_all_offers');

        Route::get('/get_products_by_category', 'get_products_by_category');

        Route::get('/get_recommanded_products', 'recommanded_products');

        Route::get('/get_all_artists', 'get_all_artists');
        Route::get('/get_artist', 'get_artist');
        Route::get('/get_all_blogs', 'get_all_blogs');
        Route::get('/get_blog', 'get_blog');
        Route::get('/get_all_photos', 'get_all_photos');
        Route::get('/get_photo', 'get_photo');
        Route::get('/get_all_events', 'get_all_events');
        Route::get('/get_event', 'get_event');
        Route::post('/contact', 'contact');

        Route::get('/about', 'about');
        Route::get('/privacy_policy', 'privacy_policy');
        Route::get('/refund_policy', 'refund_policy');

        Route::get('/terms', 'terms');
        Route::get('/profile_pic', 'profile_pic');
    });

   

    //customer Pre Authentication Routes

    Route::controller(PreAuthController::class)->group(function () {

        Route::post('/signup', 'createUser');

        Route::post('/login', 'loginUser');

        Route::post('/reset-password', 'Reset');

        Route::post('/verify-otp', 'verifyOtp');

        Route::post('/resend-otp',  'resend_otp');

       

    });

   

    //Authenticated routes

    Route::group(['middleware' => 'auth:sanctum'], function() {



        //Auth Controller

        Route::controller(AuthController::class)->group(function () {

            Route::get('/logout', 'logout');
            Route::post('/change-password', 'change_password');
            Route::post('/edit_profile', 'edit_profile');
            Route::post('/add-address', 'add_address');
            Route::get('/get-all-addresses', 'get_all_addresses');
            Route::get('/get-address', 'get_address');
            Route::get('/delete-address', 'delete_address');
            Route::post('/update-address', 'update_address');

            Route::post('/add-review', 'add_review');
            Route::post('/update-review', 'update_review');
            Route::get('/delete-review', 'delete_review');
            Route::get('/',  'user');

        });



        //Customer order routes

        Route::controller(CartController::class)->group(function () {



            Route::get('cart-list', 'cartList');

            Route::post('add-to-cart', 'addToCart');

            Route::post('update-cart', 'updateCart');

            Route::post('remove-cart', 'removeCart');

            Route::post('clear-cart', 'clearAllCart');

            Route::post('create-order', 'create_order');

            Route::post('create-checkout-order', 'create_order_by_chekout');

            Route::get('get-orders', 'get_orders');

            Route::get('get-order-detail', 'get_order_detail');

            Route::get('cancel-order', 'cancel_order');

            Route::post('update-order', 'update_order');


            Route::get('get-wishlist', 'get_wishlist');

            Route::post('add-to-wishlist', 'add_to_wishlist');

            

        });



        Route::controller(CouponController::class)->group(function () {



            Route::get('get-coupons', 'get_coupons');

            

            

        });



        Route::controller(EnqueryController::class)->group(function () {



            Route::post('/add-order', 'create_enquery');

            // Route::post('/update-order', 'update_enquery');

            // Route::get('/rooms', 'rooms');

            // Route::get('/products', 'get_products');

            // Route::post('/add_item', 'add_item');

            // Route::get('/get_details', 'get_details');

            // Route::get('/all_details', 'all_details');

            // Route::get('/enqueries', 'enqueries');

            // Route::post('/success', 'send_customer_email');

            

        });

    

          

    });



    

}); 



