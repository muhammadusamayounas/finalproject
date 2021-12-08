<?php

namespace App\Http\Controllers;
use App\Service\DatabaseConnection;
use Illuminate\Http\Request;
use App\Http\Requests\PhotoRequest;

class PhotoController extends Controller
{
    public function uploadPhoto(PhotoRequest $request)//sign_up
    {
        $connection=new DatabaseConnection();
        $path = $request->file('file');
        $array=(array)$path;
        $picture=$array["\x00Symfony\Component\HttpFoundation\File\UploadedFile\x00originalName"];
        explode('.',$picture);
        $file = $path;

        $connection->createconnection('users')->insertOne([


        ]);
 
    }

    
}
