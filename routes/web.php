<?php

use App\Http\Controllers\mailcontroller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/sendmail',[mailcontroller::class,'sendmail']);

Route::get('/',function(){
    $collection = (new MongoDB\Client)->mydatabase->users;
    $insertOneResult = $collection->insertOne([
        'username' => 'hisham',
        'email' => 'admin@example.com',
        'name' => 'Admin User',
    ]);
    return true;
});