<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginAccessRequest;
use App\Service\DatabaseConnection;
use Illuminate\Http\Request;
use App\Http\Requests\PhotoRequest;
date_default_timezone_set('Asia/Karachi');


class PhotoController extends Controller
{
    public function uploadPhoto(PhotoRequest $request)
    {
        $connection=new DatabaseConnection();
        $getimagepath=$this->storePhoto($request->file);
        $getextension= explode('.', $getimagepath[1]);
        $connection->createconnection('photos')->insertOne([
            "user_id" => $request->data->_id,
            "name" => $getextension[0],
            "photo" => $getimagepath[0],
            "extensions" => $getextension[1],
            "access" => "hidden",
            "date" => date("Y-m-d"),
            "time" => date("h:i:sa"),
        ]);   
        return response(['Message' => 'Image Added'],200);
    }

    function deletePhoto(LoginAccessRequest $request)
    {
        $connection=new DatabaseConnection();
        $id = new \MongoDB\BSON\ObjectId($request->photo_id);
        $connection->createconnection('photos')->deleteOne([
            "user_id" => $request->data->_id,
            '_id' => $id
        ]); 
        return response(['Message' => 'Deleted'],200);
    }

    function  makePublic(LoginAccessRequest $request)
    {
           $connection=new DatabaseConnection();
           $photo_id=new \MongoDB\BSON\ObjectId($request ->photo_id);
           $connection->createconnection('photos')->updateOne([
               'user_id' => $request->data->_id,
               '_id' => $photo_id],
               ['$set'=>['access' => 'Public']
            ]);
            $connection->createconnection('photos')->updateOne(
              array('_id'=>$photo_id),
              array('$unset'=>array('Email'=>''))
            );
            return response(['Message' => 'Sucessfully Updated'],200);
    }

    function  makePrivate(LoginAccessRequest $request)
    {
           $connection=new DatabaseConnection();
           $photo_id=new \MongoDB\BSON\ObjectId($request ->photo_id);
           $connection->createconnection('photos')->updateOne([
               'user_id' => $request->data->_id,
               '_id' => $photo_id],
               ['$set'=>['access' => 'Private'],
            ]);
            $connection->createconnection('photos')->updateOne([
                'user_id' => $request->data->_id,
                '_id' => $photo_id],
                ['$push'=>["Email"=>["Mail"=>$request->Email]]]
             );
            return response(['Message' => 'Sucessfully Updated'],200);
    }

    function  removeEmail(LoginAccessRequest $request)
    {
           $connection=new DatabaseConnection();
           $photo_id=new \MongoDB\BSON\ObjectId($request ->photo_id);
           $connection->createconnection('photos')->updateOne([
               "user_id"=>$request->data->_id,
               "_id" => $photo_id, 
               "Email.Mail"=>$request->Email,
               "access" => "Private"], 
               ['$pull'=>["Email"=>["Mail"=>$request->Email]]]
            );
            return response(['Message' => 'Sucessfully Removed'],200);
    }

    function  makeHidden(LoginAccessRequest $request)
    {
           $connection=new DatabaseConnection();
           $photo_id=new \MongoDB\BSON\ObjectId($request ->photo_id);
           $connection->createconnection('photos')->updateOne([
               'user_id' => $request->data->_id,
               '_id' => $photo_id],
               ['$set'=>['access' => 'Hidden']
            ]);
            $connection->createconnection('photos')->updateOne(
              array('_id'=>$photo_id),
              array('$unset'=>array('Email'=>''))
            );
            return response(['Message' => 'Sucessfully Updated'],200);
    }

    public function storePhoto($file)
    {
        $base64_string =  $file;  
        $extension = explode('/', explode(':', substr($base64_string, 0, strpos($base64_string, ';')))[1])[1]; 
        $replace = substr($base64_string, 0, strpos($base64_string, ',')+1);
        $image = str_replace($replace, '', $base64_string);
        $image = str_replace(' ', '+', $image);
        $fileName = time().'.'.$extension;
        $url= $_SERVER['HTTP_HOST'];
        $pathurl=$url."/photo/storage/app/photos/".$fileName;
        $path=storage_path('app\\photos').'\\'.$fileName;
        file_put_contents($path,base64_decode($image));
        return [$pathurl,$fileName];
    }

}
