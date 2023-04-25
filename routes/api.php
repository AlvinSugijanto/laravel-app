<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post("login", [AuthController::class, 'login']);
Route::post("register", [AuthController::class, 'register']);
Route::get("logout", [AuthController::class, "logout"]);
Route::get("verify_email/{token}",[AuthController::class,'verify_email']);
Route::post("reset_password", [AuthController::class, "reset_password"]);
Route::put("reset_password_form/{token}", [AuthController::class, "reset_password_form"]);
// Route::post("submit_reset_password", [AuthController::class, "reset_password"]);


Route::group(['middleware' => ['jwt.verify']], function() {
    
    Route::get("me", [AuthController::class, 'me']);
    Route::put("update/user/{id}", [UserController::class, 'update']);
    Route::get("users/{id}", [UserController::class, 'find']);
    Route::get("find_user", [UserController::class, 'find_user_by_username']);
    Route::put("follow/{id}", [UserController::class, 'follow']);

    Route::post("post/create", [PostController::class, 'create']);
    Route::get("post/get_my_post", [PostController::class, 'get_my_post']);
    Route::put("post/update/{id}", [PostController::class, 'update']);
    Route::delete("post/delete/{id}", [PostController::class, 'delete']);
    Route::get("post/get_by_user/{id}", [PostController::class, 'get_by_user']);
    Route::get("post/get_post_by_followed_user", [PostController::class, 'get_post_by_followed_user']);
    Route::get("post/search_post", [PostController::class, 'search_post']);

    Route::post("comment/create", [CommentController::class, 'create']);
    Route::get("comment/get_comments_by_post/{id}", [CommentController::class, 'get_comments_by_post']);
    Route::put("comment/update/{id}", [CommentController::class, 'update']);
    Route::delete("comment/delete/{id}", [CommentController::class, 'delete']);

    Route::put("like/{id_post}", [LikeController::class, 'like']);



});
