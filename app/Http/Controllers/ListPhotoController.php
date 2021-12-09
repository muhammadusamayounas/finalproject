<?php

namespace App\Http\Controllers;
use App\Http\Requests\LoginRequest;
use App\Service\DatabaseConnection;
use App\Http\Requests\LoginAccessRequest;
use Illuminate\Http\Request;

class ListPhotoController extends Controller
{
    function listPhoto(LoginAccessRequest $request) {
        $connection=new DatabaseConnection();
        $getdata=$connection->createconnection('photos')->find([
            "user_id" => $request->data->_id,
        ]); 
        $data=$getdata->toArray();
        if($data==null)
        {
          return response(['Message' => 'No Data Found'],404);
        }
        else
        {
            return response([$data]);
        }
    }
}