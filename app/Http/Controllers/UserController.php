<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;

use Auth;
use Validator;
use DB;
use Carbon\Carbon;
class UserController extends Controller
{
    
    public function update(Request $request, $id)
    {
        $data = $request->all();

        $user = User::find($id);
        if(!$user){
            return response([
                'message' => 'User not found'
            ],404);
        }
        if($id != Auth::id()){
            return response([
                'message' => 'Forbidden'
            ],401);
        }
        $validator = Validator::make($data, [
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        $data['password'] = bcrypt($request->password);

        $user->update([
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => $data['password']
        ]);
        if($user->save()){
            return response([
                'message' => 'Update Success !',
                'user'    => $user
            ],200);
        }

        
    }
    public function find($id){

        $user = User::find($id);

        if(!$user){
            return response([
                'message' => 'User not found'
            ],404);
        }
        return response([
            'message' => 'Success',
            'user'    => $user
        ],200);

    }
    public function find_user_by_username(Request $request){
        $searchTerm = $request->query('username');
        $users = User::where('username', 'LIKE', '%' . $searchTerm . '%')->get();

        return response([
            'message' => 'Success',
            'user'    => $users
        ],200);
    }

    public function follow($id){
        
        $follow = Follow::where('following_id', $id)
                        ->where('follower_id',Auth::id())
                        ->first();
        if($follow != NULL){
            $follow->delete();
            return response([
                'message' => 'Unfollow Success!',
            ],200);
        }

        Follow::create([
            'follower_id' => Auth::id(),
            'following_id' => $id,
            'created_at'   => Carbon::now()
        ]);
        return response([
            'message' => 'Follow Success!',
        ],200);
            
    

    }



}
