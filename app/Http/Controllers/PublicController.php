<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Size;
use App\Models\Style;
use App\Models\Medium;
use App\Models\Rating;
use App\Offer;
use App\Artist;
use App\Photo;
use App\Event;
use App\Blog;
use App\Contact;
use App\Models\Customer;

class PublicController extends Controller
{
    public function get_product(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string'
        ]);

        $product = Product::where(['product_id' => $request->product_id, 'status' => 1])->first();
        
        // $size = Size::where('id', $product->size)->first();
        // $style = Style::where('id', $product->style)->first();
        // $medium = Medium::where('id', $product->medium)->first();

        if($product == null){
            return response()->json(['message' => 'product can not be found', 'status' => 400], 400);
        }
        
        $ratings = $this->get_all_reviews($request->product_id);

        if($ratings){
            $product->reviews = $ratings;
        }else{
            $ratings = [];
            $product->reviews = $ratings;
        }

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

        $product->total_price = $product->price;
        $product->price -= $discount; 

        $size = Size::where('id', $product->size)->first();
        $style = Style::where('id', $product->style)->first();
        $medium = Medium::where('id', $product->medium)->first();
        $artist = Artist::where('id', $product->artist_id)->first();
        $photo = Photo::where('id', $product->photo_id)->first();
        $event = Event::where('id', $product->event_id)->first();
        $blog = Blog::where('id', $product->blog_id)->first();

        if($size !== null){
            $product->size = $size->size; 
        }
        if($style !== null){
            $product->style = $style->style; 
        }

        if($medium !== null){
            $product->medium = $medium->medium;
        }
       
       
        if($artist !== null){
            $product->artist = $artist->name; 
        }

        if($blog !== null){
            $product->blog = $blog->name; 
        }

        if($photo !== null){
            $product->photo = $photo->name; 
        }

        if($event !== null){
            $product->event = $event->name; 
        }
      
        
        $imagesArr = json_decode($product->images);
        $images = [];
        foreach ($imagesArr as $image) {
            $images[] = env('APP_URL').'/storage/'.$image;
        }
        $product->images = $images;
        // $product->size = $size->id;
        // $product->style = $style->id;
        // $product->medium = $medium->id;
        return response()->json(['product' => $product, 'status' => 200], 200);

    }



    public function get_all_products(Request $request)
    {
       

        $products = Product::where(['status' => 1])->get();

        $sizes = Size::all();
        $mediums = Medium::all();
        $styles = Style::all();
        $artists = Artist::all();
        $blogs = Blog::all();
        $photos = Photo::all();
        $events = Event::all();


        
        foreach ($artists as $artist) {
            $artist->images = env('APP_URL').'/storage/'.$artist->image;
        }
        foreach ($blogs as $blog) {
            $blog->images = env('APP_URL').'/storage/'.$blog->image;
        }
        foreach ($photos as $photo) {
            $photo->images = env('APP_URL').'/storage/'.$photo->image;
        }
        foreach ($events as $event) {
            $event->images = env('APP_URL').'/storage/'.$event->image;
        }
        if($products->isEmpty()){
            return response()->json(['message' => 'no product found', 'status' => 400], 400);
        }
       
        foreach ($products as $product) {
            $imagesArr = json_decode($product->images);
            $images = [];
            foreach ($imagesArr as $image) {
                $images[] = env('APP_URL').'/storage/'.$image;
            }
            $size = Size::where('id', $product->size)->first();
            $medium = Medium::where('id', $product->medium)->first();
            $product->images = $images;
            $product->size_name = $size->size;
            $product->medium_name = $medium->medium;
        }
        return response()->json(['products' => $products, 'sizes' => $sizes, 'mediums' => $mediums, 'styles' => $styles, 'artists' => $artists, 'blogs' => $blogs,'photos' => $photos, 'events' => $events, 'status' => 200], 200);

    }



    public function get_all_categories(Request $request)
    {
        
        $categories = Category::where(['status' => 1])->get();
        
        if($categories->isEmpty()){
            return response()->json(['message' => 'no category found', 'status' => 400], 400);
        }

        foreach ($categories as $category) {
            
            $category->icon = env('APP_URL').'/storage/'.$category->icon;
            $category->icon_white = env('APP_URL').'/storage/'.$category->icon_white;
        }
        return response()->json(['categories' => $categories, 'status' => 200], 200);

    }


    public function get_all_banners(Request $request)
    {
       

        $products = Banner::where(['status' => 1])->get();
        
        if($products->isEmpty()){
            return response()->json(['message' => 'no banner found', 'status' => 400], 400);
        }

        foreach ($products as $product) {
            
            $product->image = env('APP_URL').'/storage/'.$product->image;
        }
        return response()->json(['banners' => $products, 'status' => 200], 200);

    }



    public function filter(Request $request)
    {
       
        $request->validate([
            'size' => 'nullable|string',
            'style' => 'nullable|string',
            'orientation' => 'nullable|string',
            'medium' => 'nullable|string'
        ]);

       
        $size = explode(',', $request->size);
        $style = explode(',', $request->style);
        $medium = explode(',', $request->medium);
        $orientation = explode(',', $request->orientation);

        if($size == [""]){
            $sizeColl = Size::all();
            $size = [];
            foreach ($sizeColl as $item) {
                $size[] = $item->id;
            }
        }

        if($style == [""]){
            $styleColl = Style::all();
            $style = [];
            foreach ($styleColl as $item) {
                $style[] = $item->id;
            }
        }

        if($medium == [""]){
            $mediumColl = Medium::all();
            $medium = [];
            foreach ($mediumColl as $item) {
                $medium[] = $item->id;
            }
        }

        if($orientation == [""]){
            $orientation = [1, 2];
        }
        
        

        $products = Product::whereIn('size', $size)->whereIn('style', $style)->whereIn('medium', $medium)->whereIn('orientation', $orientation)->get();
      
      
        
        

        // $products =  $productColl->unique();
       
        if($products->isEmpty()){
            return response()->json(['message' => 'no products found', 'status' => 400], 400);
        }

        foreach ($products as $product) {
            
            $product->image = env('APP_URL').'/storage/'.$product->image;
        }
        return response()->json(['banners' => $products, 'status' => 200], 200);

    }

    private function get_all_reviews($product_id)
    {
        

        $ratings = Rating::where('product_id', $product_id)->get();

        if($ratings->isEmpty()){
            return 0;
        }

        foreach ($ratings as $rating) {
            $user = Customer::where('id', $rating->customer_id)->first();
            $rating->customer_name = $user->name;
            $rating->avatar = env('APP_URL').'/storage/'.$user->avatar;
        }

        return $ratings;
    }

    public function get_all_offers()
    {
        $offers = Offer::all();

        if($offers->isEmpty()){
            return response()->json(['message' => 'no offers found', 'status' => 200], 200);
        }

        foreach ($offers as $offer) {
             
            $offer->banner = env('APP_URL').'/storage/'.$offer->banner;
            
        }

        return response()->json(['offers' => $offers, 'status' => 200], 200);
    }

    public function recommanded_products()
    {
        $products = Product::where(['is_recommanded' => 1, 'status' => 1])->get();

        if($products->isEmpty()){

            return response()->json(['message' => 'no products found', 'status' => 200], 200);

        }

        $sizes = Size::all();
        $mediums = Medium::all();
        $styles = Style::all();

        foreach ($products as $product) {
            $imagesArr = json_decode($product->images);
            $images = [];
            foreach ($imagesArr as $image) {
                $images[] = env('APP_URL').'/storage/'.$image;
            }
            $product->images = $images;
        }
        return response()->json(['products' => $products, 'sizes' => $sizes, 'mediums' => $mediums, 'styles' => $styles, 'status' => 200], 200);


    }


    public function get_products_by_category(Request $request)
    {
        $request->valdiate([
          'cat_id' => 'required' 
        ]);

        $products = Product::where(['cat_id' => $request->cat_id, 'status' => 1])->get();

        if($products->isEmpty()){

            return response()->json(['message' => 'no products found', 'status' => 200], 200);

        }

        $sizes = Size::all();
        $mediums = Medium::all();
        $styles = Style::all();

        foreach ($products as $product) {
            $imagesArr = json_decode($product->images);
            $images = [];
            foreach ($imagesArr as $image) {
                $images[] = env('APP_URL').'/storage/'.$image;
            }
            $product->images = $images;
        }
        return response()->json(['products' => $products, 'sizes' => $sizes, 'mediums' => $mediums, 'styles' => $styles, 'status' => 200], 200);


    }


    public function get_all_artists()
    {
        $artists = Artist::all();
        if($artists->isEmpty()){
            return response()->json(['message' => 'no artist found', 'status' => 200], 200);
        }

        foreach ($artists as $artist) {
            $artist->image = env('APP_URL').'/storage/'.$artist->image;
        }

        return response()->json(['artists' => $artists, 'status' => 200], 200);
    }



    public function get_artist(Request $request)
    {
        $request->validate([
            'artist_id' => 'required'
        ]);
        $artist = Artist::where('id', $request->artist_id)->first();;
        if($artist ==  null){
            return response()->json(['message' => 'no artist found', 'status' => 200], 200);
        }

       
        $artist->image = env('APP_URL').'/storage/'.$artist->image;
       

        return response()->json(['artist' => $artist, 'status' => 200], 200);
    }

    public function get_all_blogs()
    {
        $blogs = Blog::all();
        if($blogs->isEmpty()){
            return response()->json(['message' => 'no blog found', 'status' => 200], 200);
        }

        foreach ($blogs as $blog) {
            $blog->image = env('APP_URL').'/storage/'.$blog->image;
        }

        return response()->json(['blogs' => $blogs, 'status' => 200], 200);
    }



    public function get_blog(Request $request)
    {
        $request->validate([
            'blog_id' => 'required'
        ]);
        $blog = Blog::where('id', $request->blog_id)->first();;
        if($blog ==  null){
            return response()->json(['message' => 'no blog found', 'status' => 200], 200);
        }

       
        $blog->image = env('APP_URL').'/storage/'.$blog->image;
       

        return response()->json(['blog' => $blog, 'status' => 200], 200);
    }

    public function get_all_photos()
    {
        $photos = Photo::all();
        if($photos->isEmpty()){
            return response()->json(['message' => 'no photo found', 'status' => 200], 200);
        }

        foreach ($photos as $photo) {
            $photo->image = env('APP_URL').'/storage/'.$photo->image;
        }

        return response()->json(['photos' => $photos, 'status' => 200], 200);
    }



    public function get_photo(Request $request)
    {
        $request->validate([
            'photo_id' => 'required'
        ]);
        $photo = Photo::where('id', $request->photo_id)->first();;
        if($photo ==  null){
            return response()->json(['message' => 'no photo found', 'status' => 200], 200);
        }

       
        $photo->image = env('APP_URL').'/storage/'.$photo->image;
       

        return response()->json(['photo' => $photo, 'status' => 200], 200);
    }

    public function get_all_events()
    {
        $events = Event::all();
        if($events->isEmpty()){
            return response()->json(['message' => 'no event found', 'status' => 200], 200);
        }

        foreach ($events as $event) {
            $event->image = env('APP_URL').'/storage/'.$event->image;
        }

        return response()->json(['events' => $events, 'status' => 200], 200);
    }



    public function get_event(Request $request)
    {
        $request->validate([
            'event_id' => 'required'
        ]);
        $event = Event::where('id', $request->event_id)->first();;
        if($event ==  null){
            return response()->json(['message' => 'no event found', 'status' => 200], 200);
        }

       
        $event->image = env('APP_URL').'/storage/'.$event->image;
       

        return response()->json(['event' => $event, 'status' => 200], 200);
    }


    public function contact(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'subject' => 'required|string',
            'message' => 'nullable|string'
        ]);

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->mobile = $request->mobile;
        $contact->subject = $request->subject;
        $contact->message = $request->message;
       
        if($contact->save()){
            return response()->json(['message' => 'your message has been saved', 'status' => 200], 200);
        }

        return response()->json(['message' => 'your message can not be saved', 'status' => 400], 400);
        
    }


    public function about()
    {
        $about = [];
        $name  = setting('about.about_heading');
        $image = env('APP_URL').'/storage/'.setting('about.banner');
        $content = setting('about.content');
        $about['banner'] = $image;
        $about['content'] = $content;
        $about['name'] = $name;
        return response()->json(['about' => $about, 'status' => 200], 200);
    }

    public function terms()
    {
        $about = [];
        $name  = setting('terms.heading');
        $image = env('APP_URL').'/storage/'.setting('terms.banner');
        $content = setting('terms.content');
        $about['banner'] = $image;
        $about['content'] = $content;
        $about['name'] = $name;
        return response()->json(['about' => $about, 'status' => 200], 200);
    }
    

    public function privacy_policy()
    {
        $about = [];
        $name  = setting('privacy-policy.heading');
        $image = env('APP_URL').'/storage/'.setting('privacy-policy.banner');
        $content = setting('privacy-policy.content');
        $about['banner'] = $image;
        $about['content'] = $content;
        $about['name'] = $name;
        return response()->json(['about' => $about, 'status' => 200], 200);
    }

    public function refund_policy()
    {
        $about = [];
        $name  = setting('refund-policy.heading');
        $image = env('APP_URL').'/storage/'.setting('refund-policy.banner');
        $content = setting('refund-policy.content');
        $about['banner'] = $image;
        $about['content'] = $content;
        $about['name'] = $name;
        return response()->json(['about' => $about, 'status' => 200], 200);
    }


    public function profile_pic()
    {
        
        $profile_icon = env('APP_URL').'/storage/'.setting('profile-icon.profile_icon');
      
       
        return response()->json(['profile_pic' => $profile_icon, 'status' => 200], 200);
    }


    
}
