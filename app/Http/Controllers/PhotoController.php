<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginAccessRequest;
use App\Service\DatabaseConnection;
use Illuminate\Http\Request;
use App\Http\Requests\PhotoRequest;
use App\Helpers\Base64DecodeHelper;
use App\Http\Requests\DeletePhotoRequest;
use App\Http\Requests\MakePublicHiddenRequest;

date_default_timezone_set('Asia/Karachi');


class PhotoController extends Controller
{
    public function uploadPhoto(PhotoRequest $request)
    {
        $connection=new DatabaseConnection();
        $photo=Base64DecodeHelper::decode($request->photo);
        $getextension= explode('.', $photo[1]);
        $connection->createconnection('photos')->insertOne([
            "user_id" => $request->data->_id,
            "name" => $request->filename,
            "photo" => $photo[0],
            "extensions" => $getextension[1],
            "access" => "hidden",
            "date" => date("Y-m-d"),
            "time" => date("h:i:sa"),
        ]);   
        return response(['Message' => 'Image Added'],200);
    }

    function deletePhoto(DeletePhotoRequest $request)
    {
        $connection=new DatabaseConnection();
        $id = new \MongoDB\BSON\ObjectId($request->photo_id);
        $connection->createconnection('photos')->deleteOne([
            "user_id" => $request->data->_id,
            '_id' => $id
        ]); 
        return response(['Message' => 'Deleted'],200);
    }

    function  makePublic(MakePublicHiddenRequest $request)
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

    function  makePrivate(MakePublicHiddenRequest $request)
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
                ['$push'=>["Email"=>["Mail"=>$request->email]]]
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

    function  makeHidden(MakePublicHiddenRequest $request)
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
}
