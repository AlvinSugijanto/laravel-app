<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\Comment;
use Validator;
use Auth;

class CommentController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'content' => 'required|string',
            'post_id' => 'required'
        ]);

        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        $comment = Comment::create([
            'content' => $data['content'],
            'post_id' => $data['post_id'],
            'user_id' => Auth::id()
        ]);
        if($comment){
            return response([
                'message' => 'Post Successfully Created',
                'data'    => $comment
            ],201);
        }

    }
    public function get_comments_by_post($id_post)
    {
        $comment = Comment::where('post_id', $id_post)->paginate(10);

        
        return response([
            'message' => 'Retrieve Success',
            'data'    => $comment
        ],200);
    }
    public function update($id_comment, Request $request)
    {
        $data = $request->all();

        $comment = Comment::find($id_comment);
        if(!$comment){
            return response([
                'message' => 'Comment not found'
            ],404);
        }
        if($comment->user_id != Auth::id()){
            return response([
                'message' => 'Forbidden'
            ],401);
        }
        if($request->post_id != $comment->post_id){
            return response([
                'message' => 'Forbidden'
            ],401);
        }
        $validator = Validator::make($data, [
            'content' => 'required',
            'post_id' => 'required'
        ]);
        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }
        $comment->update([
            'content' => $data['content'],
            'post_id' => $data['post_id']
        ]);
        if($comment->save())
        {
            return response([
                'message' => 'Update Post Success !',
                'post'    => $comment
            ],200);
        }
    }
    public function delete($id_comment)
    {
        $comment = Comment::find($id_comment);

        if(!$comment){
            return response([
                'message' => 'Comment not found'
            ],404);
        }
        if($comment->user_id != Auth::id()){
            return response([
                'message' => 'Forbidden'
            ],401);
        }

        if($comment->delete()){
            return response([
                'message' => 'Comment has been deleted',
            ],200);
        }
    }
}
