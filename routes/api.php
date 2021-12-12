<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CreatePostController;
use App\Http\Controllers\ReadPostController;
use App\Http\Controllers\DeletePostController;
use App\Http\Controllers\UpdatePostController;
use App\Http\Controllers\UserInfoController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RequestController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
    

Route::group(['middleware'=>'api','perfix'=>'auth'],function($router){
    
    Route::post('/register',[UserController::class,'register']);
    Route::post('/login',[UserController::class,'login']);
    Route::get('/welcome/{email}/{verify_email}',[UserController::class,'welcome']);
    Route::post('/logout',[UserController::class,'logout'])->middleware('customauth');
    Route::post('/userinformation',[UserController::class,'userInformation'])->middleware('customauth');
    Route::post('/updateuser',[UserController::class,'updateUser'])->middleware('customauth');


});






