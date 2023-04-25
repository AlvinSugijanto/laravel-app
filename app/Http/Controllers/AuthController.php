<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\VerifyEmail;
use App\Mail\UserVerification;
use App\Mail\ResetPassword;

use Mail; 
use DB;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{

    public function register(Request $request) {
        $registrationData = $request->all();

        $validator = Validator::make($registrationData, [
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
 
        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }
        
        $registrationData['password'] = bcrypt($request->password);

        $user = User::create([
            'username' => $registrationData['username'],
            'email' => $registrationData['email'],
            'password' => $registrationData['password'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $token = Str::random(60);

        DB::table('verify_email')->insert([
            'token' => $token,
            'user_id' => $user->user_id,
        ]);

        Mail::to($user->email)->send(new UserVerification($user, $token));
 
        return response()->json([
            "message" => "Successfully Registered! Please Check Your Email",
            "data" => $user
        ],201);    
    }
 
 
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $loginData = $request->all();

        $validator = Validator::make($loginData, [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        
        $credentials = request(['email', 'password']);
 
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }
        if(Auth::user()->email_verified_at == NULL){
            Auth::logout();
            return response()->json(['error' => 'Please Verify Your Email To Continue Logging In'], 401);
        }
        return response()->json([
            "message" => "Logged in successfully",
            "access_token" => $token
        ],200);        // $user = User::where('email', $request->email)->first();
        // if(!$user){
        //     return response(['message' => 'Email Not Found !'], 401);
        // }
        // if(!Hash::check($request->password, $user->password)){
        //     return response(['message' => 'Invalid Password !'], 401);
        // }
        // return $this->respondWithToken($user);
    }
 
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }
 
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
 
        return response()->json(['message' => 'Successfully logged out'],200);
    }
 
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify_email($token)
    {
        
        $verify = VerifyEmail::where('token', $token)->first();
        if(!$verify)
            return response()->json(['message' => 'Incorrect Token'], 404);

        $user = User::find($verify->user_id);
        
        if(!$user)
            return response()->json(['message' => 'User Not Found'], 404);

        if($user->email_verified_at != NULL)
            return response()->json(['message' => 'User Has Been Verified'], 200);

        $user->email_verified_at = Carbon::now();
        if($user->save())
            return response()->json(['message' => 'User Successfully Verified'], 200);

    }

    public function reset_password(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json(['message' => 'User Not Found'], 404);
        }

        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $request->email, 
            'token' => $token, 
            'created_at' => Carbon::now()
        ]);
        Mail::to($user->email)->send(new ResetPassword($request->email, $token));

        return response()->json([
            "message" => "Please check your email to reset your password",
            "link"    => url('api/reset_password_form/'.$token)
        ],200);

    }

    public function reset_password_form(Request $request, $token)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'email' => 'required',
            'new_password' => 'required|min:8',
        ]);
 
        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }


        $email = DB::table('password_resets')->where('token', $token)->first();

        if($request->email != $email->email){
            return response()->json(['message' => 'Incorrect Token !'], 400);
        }

        $user = User::where('email', $request->email)
                    ->update(['password' => Hash::make($request->new_password)]);

        DB::table('password_resets')->where(['email'=> $request->email])->delete();

        return response()->json([
            "message" => "Password has been updated !",
        ],200);


    }
}