<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Response;

class AllResponseProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('notFound',function(){

            return response()->json([

                'message' => "Not Found"

            ],404);

        });


        Response::macro('success',function(){

            return response()->json([

                'message' => "Successfully Done"

            ],200);

        });

        Response::macro('verify',function(){

            return response()->json([

                'message' => "Please Verify Your Account"

            ],401);

        });

        Response::macro('error',function(){

            return response()->json([

                'message' => "Error"

            ],402);

        });
    }
}

