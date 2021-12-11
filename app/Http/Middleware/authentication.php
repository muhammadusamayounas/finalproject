<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Service\DatabaseConnection;

class authentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
      $key = $request->bearerToken(); 
      $connection=new DatabaseConnection();
      $get=$connection->createconnection("users")->findOne([
            'remember_token' => $key
      ]);
      if($get==NULL)
        {
            return response()->json(['message'=>'Kindly Login First']);
        }
        else
        {
          return $next($request->merge(["data"=>$get]));
        }
     }
}
