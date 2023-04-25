<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Validator;
use Auth;
use DB;

class PostController extends Controller
{

    public function create(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'content' => 'required|string',
        ]);

        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        $post = Post::create([
            'content' => $data['content'],
            'user_id' => Auth::id()
        ]);
        if($post){
            return response([
                'message' => 'Post Successfully Created',
                'data'    => $post
            ],201);
        }

    }
    public function get_my_post()
    {
        $post = Post::where('user_id',Auth::id())->paginate(10);
        
        return response([
            'message' => 'Retrieve Success',
            'data'    => $post
        ],200);
    }
    public function update($id, Request $request)
    {
        $data = $request->all();

        $post = Post::find($id);
        if(!$post){
            return response([
                'message' => 'Post not found'
            ],404);
        }
        if($post->user_id != Auth::id()){
            return response([
                'message' => 'Forbidden'
            ],401);
        }
        $validator = Validator::make($data, [
            'content' => 'required',
        ]);
        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }
        $post->update([
            'content' => $data['content'],
        ]);
        if($post->save())
        {
            return response([
                'message' => 'Update Post Success !',
                'post'    => $post
            ],200);
        }
    }
    public function delete($id)
    {
        $post = Post::find($id);
        if(!$post){
            return response([
                'message' => 'Post not found'
            ],404);
        }
        if($post->user_id != Auth::id()){
            return response([
                'message' => 'Forbidden'
            ],401);
        }

        if($post->delete()){
            return response([
                'message' => 'Post has been deleted',
            ],200);
        }
    }
    public function get_by_user($id)
    {
        $post = Post::where('user_id',$id)->paginate(10);

        return response([
            'message' => 'Retrieve Success!',
            'data'    => $post
        ],200);
    }

    public function get_post_by_followed_user()
    {
        $post = DB::table('posts')
                    ->select('posts.*')
                    ->join('users','users.user_id','=','posts.user_id')
                    ->join('follows','follows.following_id','=','posts.user_id')
                    ->get();
        return response([
            'message' => 'Retrieve Success!',
            'data'    => $post
        ],200);
    }
    public function search_post(Request $request)
    {
        $searchTerm = $request->query('keyword');
        $post = Post::where('content', 'LIKE', '%' . $searchTerm . '%')->get();
        return response([
            'message' => 'Success',
            'post'    => $post
        ],200);

    }

}
