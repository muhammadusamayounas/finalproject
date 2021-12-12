<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginAccessRequest;
use App\Http\Requests\SearchPhotoRequest;
use App\Service\DatabaseConnection;
use Illuminate\Http\Request;

class SearchPhotoController extends Controller
{
    public function searchImage(SearchPhotoRequest $request)
    {
     $check=[];
     $connection=new DatabaseConnection();        
     $check['user_id'] = $request->data->_id;

     if($request->date != NULL)
      { 
          $check['date'] = $request -> date; 
      }
     if($request->time != NULL) 
      {
         $check['time'] = $request -> time; 
      }
     if($request->name != NULL) 
      { 
          $check['name'] = $request -> name; 
      }
     if($request->extensions != NULL) 
      {
         $check['extensions'] = $request -> extensions; 
      }
     if($request->accessor != NULL)
      {
         $check['access'] = $request -> access; 
      }
        $response=$connection->createconnection("photos")->find($check);
        if($response==NULL)
        {
            return response()->notFound();
        }
        else
        {
            return response([$response->toArray()]);
        }
    }
}
