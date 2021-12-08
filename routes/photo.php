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
use App\Http\Controllers\PhotoController;


Route::group(['middleware'=>'customauth'],function($router)
{
    Route::post('/uploadphoto',[PhotoController::class,'uploadPhoto']); 
});

?>