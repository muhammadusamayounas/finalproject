<?php

namespace App\Helpers;

class Base64DecodeHelper{
    public static function decode($profilephoto)
    {       
        $base64_string = $profilephoto;  
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

?>