<?php



namespace App\Http\Controllers\Customer;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Address;
use App\Models\Rating;
use App\Models\Customer;
use Hash;



class AuthController extends Controller

{

   /**

    * Logout user (Revoke the token)

    *

    * @return [string] message

    */

    public function logout()

    {

        

        Auth::user()->tokens()->delete();



        return response()->json([

            'message' => 'Successfully logged out',

            'status' => 200

            

        ], 200);



    }





    /**

    * Get the authenticated User

    *

    * @return [json] user object

    */

    public function user(Request $request)

    {



        $user = Auth::user();

        $user->avatar = env('APP_URL').'/storage/customers/'.$user->avatar;



        return response()->json(['customer' => $user, 'status' => 200], 200);

    }





    public function edit_profile(Request $request)

    {

        
        $request->validate([

            'name' => 'nullable|string',

            'email'=>'nullable|string',

            'mobile' => 'nullable|string'

        ]);
        if($request->email !== null){
            $pre_user = Customer::where('email', $request->email)->first();
            if($pre_user !== null && $pre_user->email !== $request->email){
                return response()->json(['message' => 'user email is already exist', 'status' => 400], 400);
            }
          
        }
        if($request->mobile !== null){
            $pre_user = Customer::where('mobile', $request->mobile)->first();
            if($pre_user !== null && $pre_user->mobile !== $request->mobile){
                return response()->json(['message' => 'user mobile is already exist', 'status' => 400], 400);
            }
           
        }

        $user = Auth::user();

        

        if($request->hasFile('images')){

            $filename = time().$request->images->getClientOriginalName();

        

            $request->images->storeAs('customers',$filename,'public');

           
            $user->avatar = $filename;
            $user->save();

        }



        if($user->update($request->all())){
            
            $user->avatar = env('APP_URL').'/storage/customers/'.$user->avatar;
            

            return response()->json([

                'status' => 200,

                'message' => 'updated successfully',
                'Customer' => $user
            ], 200);

        }else{

            return response()->json([

                'status' => 400,

                'message' => 'field does not exist',

            ], 400);

        }

        

    }

    

    /**

     * Upload Profile Pic

     * @param Request $request

     */

    public function profile_pic(Request $request)

    {

        $user = Auth::user();

        

        if($request->hasFile('avatar')){

            $filename = time().$request->avatar->getClientOriginalName();

            $request->avatar->storeAs('customers',$filename,'public');

            $user->update(['avatar'=> $filename]);

            return response()->json([

                'status' => 200,

                'message' => 'file has been uploaded',

            ], 200);

        }else{

            return response()->json([

                'status' => 400,

                'message' => 'please choose image file',

            ], 400);

        }  

        

    }





    /**

     * Delete Profile Pic

     * @param Request $request 

     */

    public function delete_pic()

    {

        $user = Auth::user();

        $user->avatar = 'default.png';



        if($user->save()){

            

            return response()->json([

                'status' => 200,

                'message' => 'profile pic has been deleted',

            ], 200);

        }else{

            return response()->json([

                'status' => 400,

                'message' => 'can not delete profile pic',

            ], 400);

        }    

    }
     
    /**
     * change password
     * @param Request $request
     * @return Vendor
     */
    public function change_password(Request $request)
    {
        $request->validate([
        'old_psw' => 'required|string',
        'new_psw' => 'required|string'
        ]);
        
        $user = Auth::user();
        #check password
        if(Hash::check($request->old_psw , $user->password) ){

            #Update the new Password
            Customer::whereId(auth()->user()->id)->update([
                'password' => bcrypt($request->new_psw)
            ]);

            
            return response()->json([
            'status' => 200,
            'message' => 'password change successfully',
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'old password does not match'
            ]);
        }
             
        
        
    }

    public function add_address(Request $request)
    {
        $request->validate([

          'name' => 'required|string',
          'mobile' => 'required',
          'street' => 'required',
          'landmark' => 'nullable',
          'city' => 'required',
          'state' => 'nullable',
          'country' => 'nullable',
          'pin_code' => 'required',
          'type' => 'required',
          'is_default' => 'required'

        ]);

        $address = new Address();
        $address->name = $request->name; 
        $address->mobile = $request->mobile; 
        $address->street = $request->street; 
        $address->landmark = $request->landmark; 
        $address->city = $request->city; 
        $address->state = $request->state; 
        $address->pin_code = $request->pin_code; 
        $address->country = $request->country; 
        $address->type = $request->type; 
        $address->user_id = Auth::user()->id;

        if($address->save()){
            if($request->is_default == 1){
                $user = Auth::user();
                $user->default_address = $address->id; 
                $user->save();
            }

            return response()->json(['message' => 'address has been saved', 'status' =>200], 200);
        }

        return response()->json(['message' => 'address can not be saved', 'status' => 400], 400);
        
    }

    public function get_all_addresses(Request $request)
    {
        

        $addresses = Address::where(['user_id' => Auth::user()->id])->get();

        if($addresses->isEmpty()){
            return response()->json(['message' => 'no address found', 'status' => 400], 400);
        }

        return response()->json(['addresses' => $addresses, 'status' =>200], 200);

        

        
        
    }

    public function get_address(Request $request)
    {
        $request->validate([

            'address_id' => 'required'

        ]);

        $address = Address::where(['id' => $request->address_id, 'user_id' => Auth::user()->id])->first();

        if($address == null){
            return response()->json(['message' => 'address can not be found', 'status' => 400], 400);
        }

        return response()->json(['address' => $address, 'status' =>200], 200);

        

        
        
    }


    public function delete_address(Request $request)
    {
        $request->validate([

            'address_id' => 'required',
            'name' => 'nullable',
            'mobile' => 'nullable',
            'street' => 'nullable',
            'landmark' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'country' => 'nullable',
            'pin_code' => 'nullable',
            'type' => 'nullable',
            'is_default' => 'nullable'
        ]);

        $address = Address::where(['id' => $request->address_id, 'user_id' => Auth::user()->id])->first();

        if($address == null){
            return response()->json(['message' => 'address can not be found', 'status' => 400], 400);
        }

        if($address->delete()){
            
    
            return response()->json(['message' => 'address has been deleted', 'status' =>200], 200);
        }
        

        
        return response()->json(['message' => 'address can not be deleted', 'status' => 400], 400);
        
        
    }


    public function update_address(Request $request)
    {
        $request->validate([

            'address_id' => 'required'

        ]);

        $address = Address::where(['id' => $request->address_id, 'user_id' => Auth::user()->id])->first();

        if($address == null){
            return response()->json(['message' => 'address can not be found', 'status' => 400], 400);
        }

        if($address->update($request->all())){
            
    
            return response()->json(['message' => 'address has been updated', 'address' => $address, 'status' =>200], 200);
        }
        

        
        return response()->json(['message' => 'address can not be updated', 'status' => 400], 400);
        
        
    }

    public function add_review(Request $request)
    {
        $request->validate([
            'rating' => 'required',
            'review' => 'nullable|string',
            'product_id' => 'required|string'
        ]);

        $rating = new Rating();
        $rating->rating = $request->rating;
        $rating->review = $request->review;
        $rating->customer_id = Auth::user()->id;
        $rating->product_id = $request->product_id;
        $rating->status = 0;

        if($rating->save()){
            return response()->json(['message' => 'review has been saved', 'status' => 200], 200);
        }

        return response()->json(['message' => 'review can not be saved', 'status' => 400], 400);
    }


    public function update_review(Request $request)
    {
        $request->validate([
            'rating' => 'nullable',
            'review' => 'nullable|string',
            'review_id' => 'required|string'
        ]);

        $rating =  Rating::where(['id' => $request->review_id, 'customer_id' => Auth::user()->id  ])->first();
        if($rating == null){
            return response()->json(['message' => 'review can not be found', 'status' => 400], 400);
        }
        if($request->rating !== null){
            $rating->rating = $request->rating;
        }
        if($request->review !== null){
            $rating->review = $request->review;
        }
       

        if($rating->save()){
            return response()->json(['message' => 'review has been updated', 'status' => 200], 200);
        }

        return response()->json(['message' => 'review can not be updated', 'status' => 400], 400);
    }

    public function delete_review(Request $request)
    {
        $request->validate([
            
            'review_id' => 'required|string'
        ]);

        $rating =  Rating::where(['id' => $request->review_id, 'customer_id' => Auth::user()->id  ])->first();
        if($rating == null){
            return response()->json(['message' => 'review can not be found', 'status' => 400], 400);
        }
        

        if($rating->delete()){
            return response()->json(['message' => 'review has been deleted', 'status' => 200], 200);
        }

        return response()->json(['message' => 'review can not be deleted', 'status' => 400], 400);
    }

}

