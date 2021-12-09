<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListPhotoController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\PhotoController;


Route::group(['middleware'=>'customauth'],function($router)
{
    Route::post('/uploadphoto',[PhotoController::class,'uploadPhoto']); 
    Route::post('/deletephoto',[PhotoController::class,'deletePhoto']); 
    Route::post('/listphoto',[ListPhotoController::class,'listPhoto']); 


    Route::post('/makepublic',[PhotoController::class,'makePublic']); 
    Route::post('/makeprivate',[PhotoController::class,'makePrivate']); 
    Route::post('/makehidden',[PhotoController::class,'makeHidden']); 




});

?>