<?php



namespace App\Http\Controllers\Customer;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Customer;

use App\Models\VerificationCode;

use carbon\Carbon;

use Illuminate\Support\Facades\Auth;







class PreAuthController extends Controller

{

    /**

     * Create Customer

     * @param Request $request

     */

    public function createUser(Request $request)

    {

        

        $request->validate([

            'name' => 'required|string',

            'email'=>'required|email',

            'gender'=>'nullable|string',

            'password'=>'required|string',

            'device_id' => 'nullable|string',

           

            

        ]);



        if($request->mobile == null){

            return response()->json([

                'status' => 400,

                'message' => 'mobile no. is required'

            ], 400);

        }



        $usercheck = Customer::where(['email' => $request->email])->orWhere(['mobile' => $request->mobile])->first();



        if($usercheck !== null ){

            return response()->json([

                'status' => 400,

                'message' => 'user already exit'

            ], 400); 



        }

        

        

        $user = new Customer([

            'name'  => $request->name,

            'email' => $request->email,

            'password' => bcrypt($request->password),

            'gender' => $request->gender,

            'mobile' => $request->mobile,

            'avatar' => 'default.png',

            'device_id' => $request->device_id,

           

        ]);



        if($user->save()){

            

            $user->avatar = env('APP_URL').'/storage/customers/default.png';



            $verificationCode = $this->generateOtp($user);
            

            $msg = 'this is your otp - '.$verificationCode->otp;
            
            $data = [

                'msg'   => $msg, 

                'email' => $request->email

            

            ];



            //sending password through email

            \Mail::send('otp_mail', $data, function($message) use ($data) {

                $message->to($data['email'], 'Techninza.in')->subject

                ('Your OTP for verification');

                $message->from('sanurag0022@gmail.com','Techninza admin');

            });

            return response()->json([

            'status' => 200,

            'message' => 'Successfully created user!',
            
            'otp' => $msg
           

           

            ], 200);

        }



        

        else{

            return response()->json(['message'=>'can not create user', 'status' => 400], 400);

        }

    }





    /**

     * Login The User

     * @param Request $request

     * @return Customer

     */

    public function loginUser(Request $request)

    {

        $request->validate([

        'email' => 'required|string|email',

        'password' => 'required|string',

        'remember_me' => 'boolean',

        'device_id' => 'nullable|string',

      

        ]);

        

        $usercheck = Customer::where(['email' => $request->email])->first();

        

        if($usercheck == null ){

            return response()->json([

                'status' => 400,

                'message' => 'user not found'

            ], 400); 

        }elseif($usercheck->mobile_verify_at == null){

            return response()->json([

                'status' => 403,

                'message' => 'account not verified',

               

            ], 403);

        }else{

            $usercheck->device_id = $request->device_id;

          

            $usercheck->save();



            $credentials = request(['email','password']);

            if(!Auth::guard('customer')->attempt($credentials))

            {

                return response()->json([

                    'status' => 400,

                    'message' => 'wrong password'

                ], 400);

            } 

        }



        $user = Auth::guard('customer')->user();

        $user->avatar = env('APP_URL').'/storage/users/'.$user->avatar;



        



        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->plainTextToken;

    

        return response()->json([

        'status' => 200,

        'accessToken' => $token,

        'token_type' => 'Bearer',

        'Customer' => $user,

        'message' => 'Login Successful'

       

        ], 200);

    }





    /**

     * forgot password API

     * @param Request $request

     */

    public function Reset(Request $request)

    {

        $request->validate([

            'email' => 'required|email',

            

        ]);



        



        $usercheck = Customer::where(['email' => $request->email])->first();



        if($usercheck == null ){



            return response()->json([

                'status' => 400,

                'message' => 'user not found'

            ],400);



        }else{

            

            $length = 10;

            $str = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';

            $new_pass = substr(str_shuffle($str), 0, $length);

            $password = bcrypt($new_pass);

            

            $data = [

                'email'   => $request->email, 

                'new_pass' => $new_pass

            

            ];



            //sending password through email

            \Mail::send('mail', $data, function($message) use ($data) {

                $message->to($data['email'], 'Techninza.in')->subject

                ('Your New Password');

                $message->from('sanurag0022@gmail.com','Techninza admin');

            });



            $usercheck->password = $password;

            $usercheck->save();



            return response()->json([

                'message' => 'password has been sent to your email',

                'status' => 200,

                'new_pass' => $new_pass,

                

            ], 200);



        }





    }





    //otp verification methods



    /**

     * generating OTP

     * @param Request $request

     */



    private function generateOtp($user)

    {

       // $user = User::where('mobile', $mobile)->first();



       



        # User Does not Have Any Existing OTP

        $verificationCode = VerificationCode::where(['user_id' => $user->id, 'is_vendor' => 0])->latest()->first();



        $now = Carbon::now();



        if($verificationCode && $now->isBefore($verificationCode->expire_at)){

            return $verificationCode;

        }



        // Create a New OTP

        return VerificationCode::create([

            'user_id' => $user->id,

            'otp' => rand(1234, 9999),

            // 'otp' => 1234,

            'is_vendor' => 0,

            'expire_at' => Carbon::now()->addMinutes(13800)

        ]);

    }



    /**

     * Resend OTP

     * @param Request $request

     * @return otp

     */



    public function resend_otp(Request $request)

    {

        #Validation

        $request->validate([

            'email' => 'required|email',

        ]);



        $user = Customer::where(['email' => $request->email])->first();



        if($user == null){

            return response()->json([

                'status' => 400,

                'message' => 'User not found',

            ], 400);

        }

        

        $verificationCode = $this->generateOtp($user);

        $msg = 'this is your otp - '.$verificationCode->otp; 
            
        $data = [

            'msg'   => $msg, 

            'email' => $request->email

        

        ];



        //sending password through email

        \Mail::send('otp_mail', $data, function($message) use ($data) {

            $message->to($data['email'], 'Techninza.in')->subject

            ('Your OTP for verification');

            $message->from('sanurag0022@gmail.com','Techninza admin');

        });
     

            return response()->json([

            'status' => 200,

            'message' => 'Sucessfully resent otp',
            
            'otp' => $msg
            

            ], 200);

        

        

    }

    



    /**

     * Verifying OTP

     * @param Request $request

     * @return Customer

     */

    public function verifyOtp(Request $request)

    {

        #Validation

        $request->validate([

            'email' => 'required|email',

            'otp' => 'required'

        ]);



        $user = Customer::where(['email' => $request->email])->first();



        if($user == null){

            return response()->json(['message' => 'User not found', 'status' => 400 ], 400);

        }

        

        #Validation Logic

        $verificationCode   = VerificationCode::where('user_id', $user->id)->where('otp', $request->otp)->first();



        $now = Carbon::now();

        if (!$verificationCode) {

            return response()->json(['message' => 'Your OTP is not correct', 'status' => 400 ], 400);

        }elseif($verificationCode && $now->isAfter($verificationCode->expire_at)){

            return response()->json(['message' => 'Your OTP has been expired', 'status' => 400 ], 400);

        }



        



        if($user){

            // Expire The OTP

            $verificationCode->update([

                'expire_at' => Carbon::now()

            ]);



            $user->update([

                'mobile_verify_at' => Carbon::now()

            ]);

            $user->avatar = env('APP_URL').'/storage/users/'.$user->avatar;

            $tokenResult = $user->createToken('Personal Access Token');

            $token = $tokenResult->plainTextToken;

           



            return response()->json(['message' => 'Registration successful', 'accessToken'=> $token, 'status' => 200, 'Customer' => $user ], 200);

        }



        return response()->json(['error' => 'Your OTP is not correct', 'status' => 400 ], 400);

    }





    

}

