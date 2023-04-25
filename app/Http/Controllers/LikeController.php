<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use Auth;

class LikeController extends Controller
{
    public function like($id_post)
    {
        $like = Like::where('post_id', $id_post)
        ->where('user_id',Auth::id())
        ->first();

        if($like != NULL){
            $like->delete();
            
            return response([
                'message' => 'Unlike Success!',
                ],200);
        }
        Like::create([
            'post_id' => $id_post,
            'user_id' => Auth::id(),
        ]);
        return response([
            'message' => 'Like Success!',
        ],200);
    }
}
