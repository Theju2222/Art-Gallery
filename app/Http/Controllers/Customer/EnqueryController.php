<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquery;
use App\Models\EnqueryProduct;
use carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\User;
use App\Models\Room;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\Quotation;

class EnqueryController extends Controller
{
    /**
     * create enquery
     * @param Request $request
     */

    public function create_enquery(Request $request)
    {

        $request->validate([
            'bhk' => 'required',
            'from_location' => 'required',
            'to_location' => 'required',
            'date' => 'required',

        ]);
        
        
        $user = Auth::user();
        
        $order = new Enquery();

        $order->user_id = Auth::user()->id;
      
        $order->from_bhk = $request->bhk;
        $order->from_location =  $request->from_location;
        $order->to_location =  $request->to_location;
        $order->token = md5(rand(1, 60) . microtime());
        $order->date = Carbon::parse($request->date)->format('l, d F y');




        if($order->save()){

            // $order->name = $user->name;
            // $order->email = $user->email;
            // $order->mobile = $user->mobile;
            // $order->link = 'https://shopninja.in/anurag/budgetlogistics/enquery?token='.$order->token;

            // $orderArr = $order->toArray();

            // \Mail::send('mail', $orderArr, function($message) use ($orderArr) {
            //     $message->to('sanurag0022@gmail.com', 'Budget Logistics')->subject
            //        ('new enquery made by '.$orderArr['name']);
            //     // $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
            //     // $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
            //     $message->from($orderArr['email'], $orderArr['name']);
            //  });

            return response()->json([
            'status' => 200,
            'message' => 'data has been saved',
            'enquery_id' => $order->id,
            'token' => $order->token
            ], 200);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'data could not be saved'
            ], 400);
        }

        
    }


    /**
     * update enquery
     * @param Request $request
     */

    public function update_enquery(Request $request)
    {
        $request->validate([
            'enquery_id' => 'required',
            'bhk' => 'nullable',
            'from_floor' => 'nullable',
            'from_lift' => 'nullable',
            'from_location' => 'nullable',
            'to_floor' => 'nullable',
            'to_lift' => 'nullable',
            'to_location' => 'nullable',
            'date' => 'nullable',
            
        ]);
        
        $user = Auth::user();
        $order['from_bhk'] = $request->bhk;
        $order = Enquery::where(['id' => $request->enquery_id, 'user_id' => $user->id ])->first();

        if($order == null){
            return response()->json([
                'status' => 400,
                'message' => 'No Enquery Found!',
            ], 400);
        }

        if($order->update($request->all())){
            
            return response()->json([
                'status' => 200,
                'message' => 'Enquery saved',
                'enquery_id' => $order->id
            ], 200);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Enquery Does Not Saved',
            ], 400);
        }
    
        
    }


    /**
     * rooms
     * @param Request $request
     * @return $rooms
     */

    public function rooms(Request $request)
    {
      
       $request->validate([
           'enquiry_id' => 'required'
       ]); 
        $user = Auth::user();

        $order = Enquery::where(['user_id' => $user->id, 'id' => $request->enquiry_id ])->first();
        
        $bedrooms = [];
        
        for($exp = $order->from_bhk; $exp > 0; $exp--){
           
            $bedrooms[] = Room::where('id', 2)->first(); 

        }
           
        

        $rooms = Room::whereNotIn('id', [2])->get();
        
        if($rooms->isEmpty()){
            return response()->json([
                'status' => 400,
                'message' => 'No room found'
            ]);
        }

        foreach ($rooms as $item) {
           
            $item->icon = env('APP_URL').'/storage/rooms/'.$item->icon;
           
        }
        foreach ($bedrooms as $item) {
           
            $item->icon = env('APP_URL').'/storage/rooms/'.$item->icon;
           
        }
        
        return response()->json([
            'status' => 200,
            'rooms' => $rooms,
            'bedrooms' => $bedrooms
        ]);
    }
    
    /**
     * products
     * @param Request $request
     * @return $products
     */

    public function get_products(Request $request)
    {
      
        $request->validate([
          'room_id' => 'required'
        ]);

        $products = Product::where('room_id', $request->room_id)->get();
        
        if($products->isEmpty()){
            return response()->json([
                'status' => 400,
                'message' => 'No product found'
            ]);
        }
        
        foreach ( $products as $item) {
            $attributes = Attribute::where('product_id', $item->id)->get();
            $item->attributes = $attributes;
            $item->icon = env('APP_URL').'/storage/products/'.$item->icon;
           
        }
        
        return response()->json([
            'status' => 200,
            'products' => $products
        ]);
    }

    public function add_item(Request $request)
    {
       


        $data = $request->all();
      
       
        $enquery_id = $data['enquiry_id'];
        $product_id = $data['product_id'];
        unset($data['enquiry_id']);
        unset($data['product_id']);
        $attributes = Attribute::where(['product_id' => $product_id])->get();
      
            foreach ($data as $key => $item) {
            
              $pre_enquery_product = EnqueryProduct::where(['product_id' => $product_id, 'enquery_id' => $enquery_id, 'attribute_name' => $key])->first();
            
              if($pre_enquery_product == null){
                $enquery_products = new EnqueryProduct();
                $enquery_products->enquery_id = $enquery_id;
                $enquery_products->product_id = $product_id;
                $enquery_products->attribute_name = $key;
                $enquery_products->attribute_value = $item;
                $enquery_products->save();
              }else{
                  $pre_enquery_product->attribute_value = $item;
                  $pre_enquery_product->save();
              }
             
            }
      
        return response()->json(['message' => 'data save successfully', 'status' => 200], 200);
    }


    public function get_details(Request $request)
    {
        $request->validate([
            'enquiry_id' => 'required',
            'room_id' => 'required'
        ]);

        $user = Auth::user();

        $enquery = Enquery::where(['id' => $request->enquiry_id, 'user_id' => $user->id])->first();
       

        if($enquery == null){
            return response()->json(['message' => 'no data found!', 'status'=> 400], 400);
        }

        // $user = Customer::where('id', $enquery->user_id)->first();
        $enquery->user = $user;

        $enquery_attributes = EnqueryProduct::where(['enquery_id' => $enquery->id])->get();
        // $items = [];
        // foreach($enquery_attributes as $key => $item){
        //     $items[] = $item->product_id; 
        // }
        // $product_ids = collect($items)->unique();
        $products = [];
        $all_products = Product::where(['room_id' => $request->room_id])->get();
        foreach ($all_products as $product) {
        
            
            $attributes = EnqueryProduct::where(['enquery_id' => $request->enquiry_id, 'product_id' => $product->id])->get();
            // $attributes->makeHidden(['id','enquery_id','product_id', 'created_at', 'updated_at']);
            $products[$product->name] = $attributes;
        }

       
        
        $enquery->products = $products;

        return response()->json(['enquery' => $enquery, 'status' => 200], 200);
    }


    public function enquery_details(Request $request)
    {
        $request->validate([
            'enquiry_id' => 'required'
        ]);

        $enquery = Enquery::where('id', $request->enquiry_id)->first();
        $user = Customer::where('id', $enquery->user_id)->first();
        $enquery->user = $user;
        $enquery_attributes = EnqueryProduct::where(['enquery_id' => $enquery->id])->get();
    
        $items = [];
        foreach($enquery_attributes as $key => $item){
            $items[] = $item->product_id; 
        }
        $product_ids = collect($items)->unique();
        $products = [];
        foreach ($product_ids as $value) {
        
            $product = Product::where('id', $value)->first();
            $attributes = EnqueryProduct::where(['enquery_id' => $request->enquiry_id, 'product_id' => $product->id])->get();
            $item = $attributes->makeHidden(['id','enquery_id','product_id', 'created_at', 'updated_at']);
            $products[$product->name] = $item;
        }

       
      
        return view('enquery_detail', ['enquery' => $enquery, 'products' => $products]);
    }


    public function all_details(Request $request)
    {
        $request->validate([
            'enquiry_id' => 'required'
        ]);
        $user = Auth::user();
        $enquery = Enquery::where(['id' => $request->enquiry_id, 'user_id' => $user->id])->first();
      
        $enquery->user = $user;
        $enquery_attributes = EnqueryProduct::where(['enquery_id' => $enquery->id])->get();
    
        $products = [];
        $all_products = Product::all();
        foreach ($all_products as $product) {
        
            
            $attributes = EnqueryProduct::where(['enquery_id' => $request->enquiry_id, 'product_id' => $product->id])->get();
            // $attributes->makeHidden(['id','enquery_id','product_id', 'created_at', 'updated_at']);
            $products[$product->name] = $attributes;
        }

        $enquery->products = $products;
      
        return response()->json(['enquery' => $enquery, 'status' => 200], 200);
    }


    public function send_customer_email(Request $request)
    {
        $request->validate([
            'enquiry_id' => 'required'
        ]);

        $enquery = Enquery::where('id', $request->enquiry_id)->first();
        $quotation = new Quotation();
        $quotation->enquery_id = $enquery->id;
        $quot_no = $this->generateUniqueCode();
        $quotation->quot_no =  $quot_no;
        $quotation->save();
        $user = Auth::user();
        $user->link = 'https://shopninja.in/anurag/budgetlogistics/enquery?quot_no='.$quot_no.'&token='.$enquery->token;
     


        $userArr = $user->toArray();
        

        \Mail::send('customer_mail', $userArr, function($message) use ($userArr) {
            $message->to($userArr['email'], $userArr['name'])->subject
               ('Confirmation mail by Budget logistics About new Enquery');
            // $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
            // $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
            $message->from('sanurag0022@gmail.com', 'Budget Logistics');
        });


         \Mail::send('customer_mail', $userArr, function($message) use ($userArr) {
            $message->to('sanurag0022@gmail.com', 'Budget Logistics')->subject
               ('Confirmation mail by Budget Logistics ');
            // $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
            // $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
            $message->from($userArr['email'], $userArr['name']);
         });
      
         return response()->json(['message' => 'email sent', 'status' => 200], 200);
    }


    public function enqueries()
    {
       

        $user = Auth::user();

        $enqueries = Enquery::where(['user_id' => $user->id])->get();
       
        
        if($enqueries->isEmpty()){
            return response()->json(['message' => 'no data found!', 'status'=> 400], 400);
        }

        foreach ($enqueries as $enquery) {
           
            $planner = User::where('id', $enquery->move_planner)->first();
           
            $user = Customer::where('id', $enquery->user_id)->first();
            if($planner !== null){
                $enquery->planner_name = $planner->name;
                $enquery->planner_mobile = $planner->mobile;
            }
           
            $enquery->user = $user;
    
            $enquery_attributes = EnqueryProduct::where(['enquery_id' => $enquery->id])->get();
            $items = [];
            foreach($enquery_attributes as $key => $item){
                $items[] = $item->product_id; 
            }
            $product_ids = collect($items)->unique();
            $products = [];
            foreach ($product_ids as $value) {
            
                $product = Product::where(['id' => $value])->first();
                $attributes = EnqueryProduct::where(['enquery_id' => $enquery->id, 'product_id' => $product->id])->get();
                // $attributes->makeHidden(['id','enquery_id','product_id', 'created_at', 'updated_at']);
                $products[$product->name] = $attributes;
            }
    
           
            
            $enquery->products = $products;
        }

        

        return response()->json(['enqueries' => $enqueries, 'status' => 200], 200);
    }


    public function public_link(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'quot_no' => 'required'
        ]);
        
        $quotation = Quotation::where('quot_no', $request->quot_no)->first();

        $enquery = Enquery::where('token', $request->token)->first();
        $user = Customer::where('id', $enquery->user_id)->first();
        $enquery->user = $user;
        $enquery_attributes = EnqueryProduct::where(['enquery_id' => $enquery->id])->get();
    
        $items = [];
        foreach($enquery_attributes as $key => $item){
            $items[] = $item->product_id; 
        }
        $product_ids = collect($items)->unique();
        $products = [];
        foreach ($product_ids as $value) {
        
            $product = Product::where('id', $value)->first();
            $attributes = EnqueryProduct::where(['enquery_id' =>  $enquery->id, 'product_id' => $product->id])->get();
            $item = $attributes->makeHidden(['id','enquery_id','product_id', 'created_at', 'updated_at']);
            $products[$product->name] = $item;
        }


      
        return view('public', ['enquery' => $enquery, 'quotation' => $quotation, 'products' => $products]);
    }


    public function generateUniqueCode()
    {
        do {
            $code = random_int(100000, 999999);
        } while (Quotation::where("quot_no", "=", $code)->first());
  
        return $code;
    }
}
