<?php

namespace App\Http\Controllers;

use App\Helpers\Base64Decode;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\testmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\SignUpRequest;
use App\Http\Requests\LoginRequest;
use App\Service\DatabaseConnection;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginAccessRequest;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Base64DecodeHelper;
use Symfony\Component\HttpFoundation\Request;
use App\Helpers\GetPhoto;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth:api',['except'=>['login','register','welcome','logout','userInformation','getphoto','updateUser']]) ;
    }
    public function register(SignUpRequest $request)//sign_up
    {
        $connection=new DatabaseConnection();
        $name=$request->name;
        $email=$request->email;
        $age=$request->age;
        $password=Hash::make($request->password);
        $status=0;
        $token =$token = rand(100,1000);
        $mail=$request->email;  
        $profilephoto=Base64DecodeHelper::decode($request->profilephoto);
        $connection->createconnection('users')->insertOne([
            'name'=>$name,
            'email'=>$email,
            'age'=>$age,
            'password'=>$password,
            'file'=>$profilephoto[0],
            'status'=>$status,
            'token'=>$token,
            'email_verified'=>FALSE
        ]);
        $this->sendmail($mail,$token);
        return response()->verify();  
    }
    public function updateUser(UpdateUserRequest $request) 
    {
        $connection=new DataBaseConnection();
        if($request->name != NULL)
        {
            $update['name'] = $request->name;
        }
        if($request->email != NULL)
        {
            $update['email'] = $request->email;
        }
        if($request->password != NULL)
        {
            $update['password'] = Hash::make($request->password);
        }
        if($request->profilephoto != NULL)
        {
            $profilephoto=Base64DecodeHelper::decode($request->profilephoto);
            $update['file'] = ($request->$profilephoto[0]);
        }
        if($request->age != NULL)
        {
            $uodate['age'] = $request->age;
        }
        if(count($update) != 0) {
            $connection->createconnection('users')->updateOne(
                [ '_id' => $request->data->_id ],
                [ '$set' => $update]);
            return response()->success();
        }
        else
        {
            return response()->notFound();
        }
    }

    public function sendmail($email,$user_token)
    { 
        $url= $_SERVER['HTTP_HOST'];
        $details=[
            'title'=>'You are successfully sign up to our SocialApp',
            'body'  =>'Please Verify your Account. Please Click on this link to verify ' .$url.'/api/welcome'.'/'.$email.'/'.$user_token];

        Mail::to($email)->send(new testmail($details));
        return response()->success();
    }

    public function welcome($email, $verify_email)
    {
        $connection=new DatabaseConnection();
        $table='users';
        $connect=$connection->createconnection($table);

        $get= $connect->findOne(
        [
            'email' => $email,
            'token' => (int)$verify_email,
        ]);
        if(!empty($get))
        {
            $connect->updateMany(array("email"=>$email),
            array('$set'=>array('email_verified'=>1,'email_verified_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'))));           
            return response()->success();
        }
        else
        {
            return response()->notFound();
        }

    }

    public function login(LoginRequest $request)
     {
        $connection=new DatabaseConnection();
        $connect=$connection->createconnection("users");
        $email=$request->email;
        $password=$request->password;
        $get=$connect->findOne([
            'email' => $email
        ]);
        if($get->email_verified == 1){
            $getpassword = $get->password;
            if (Hash::check($password, $getpassword)) {
                $jwt=new TokenController($email);
                $token=$jwt->Generate_jwt(); 
                $connect->updateOne(
                    [ 'email' => $email ],
                    [ '$set' => [ 'status' => 1 ,'remember_token' => $token]]
                 );
                return response()->json(['access_token'=>$token , 'message'=> 'Login'],200);
              }
            else
            {
                return response()->error();
            }
        }
        else
        {
            return response()->verify();
        }   
    } 
    public function userInformation(LoginAccessRequest $request)
    {
        $connection=new DatabaseConnection(); 
        $information=$connection->createconnection("users")->findOne([
            '_id'=>$request->data->_id
        ]);  
        return response([$information])->success();
    }
    public function getphoto($filename)
    {
        $headers = ["Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0"];
        $path = storage_path("app/photos".'/'.$filename);
         if (file_exists($path)) {
            return response()->download($path, null, $headers, null);
        }
        return response()->notFound();
    }
    public function logout(LoginAccessRequest $request)
    {
      $request->validated();
      $key=$request->access_token;
      $connection=new DatabaseConnection();
      $get=$connection->createconnection("users")->findOne([
            'remember_token' => $key
        ]);
      if($get==NULL)
        {
            return response()->error();
        }
        else
        {
            $connection->createconnection("users")->updateOne(
                [ 'remember_token' => $key ],
                [ '$set' => [ 'status' => 0 ,'remember_token' => NULL]]
            );
            return response()->success();
        }
    }

    

}

    


