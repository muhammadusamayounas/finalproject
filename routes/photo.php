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

    Route::post('/removemail',[PhotoController::class,'removeEmail']); 


    


});

Route::any('/storage/photos/{filename}',function(Request $request, $filename){
    $headers = ["Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0"];
    $path = storage_path("app/photos".'/'.$filename);
     if (file_exists($path)) {
        return response()->download($path, null, $headers, null);
    }
    return response()->json(["error"=>"Error downloding file"],400);
});


?>