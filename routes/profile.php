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


Route::group(['middleware'=>'customauth'],function($router)
{
    Route::post('/showprofile',[UserInfoController::class,'showprofile']); 
});

?>