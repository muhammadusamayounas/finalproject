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
        $this->middleware('auth:api',['except'=>['login','register','welcome','logout','userInformation','getphoto','updateUser','forgetPassword','setNewPassword']]) ;
    }
    /**
     * Register User
     * Take 5 parameters(name,email,age,password,profilepicture)
     * After Successfull Verification of above parameters it will send mail to user to verify account
     */
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
    /**
     * Update User
     * it can take upto 5 parameters(name,email,age,password,profilepicture)
     * 
     */
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
    /**
     * Send Mail
     * Take 2 parameters(email,$user_token)
     * 
     */
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
    /**
     * User Login
     * Take 2 parameters(email,password)
     * After Successfull Verification of above parameters and after checking that user has verified his email it give access to user
     */
    public function login(LoginRequest $request)
     {
        $connection=new DatabaseConnection();
        $connect=$connection->createconnection("users");
        $email=$request->email;
        $password=$request->password;
        $get=$connect->findOne([
            'email' => $email
        ]);
        if($get==null)
        {
            return response()->error();
        }
        else{
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
    } 
      
    /**
     * User Information
     * Take 1 parameters(user_id)
     * After Successfull Verification of user it display him his information
     */
    public function userInformation(LoginAccessRequest $request)
    {
        $connection=new DatabaseConnection(); 
        $information=$connection->createconnection("users")->findOne([
            '_id'=>$request->data->_id
        ]);  
        return response([$information]);
    }
    /**
     * 
     * 
     *
     */
    public function getphoto($filename)
    {
        $headers = ["Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0"];
        $path = storage_path("app/photos".'/'.$filename);
         if (file_exists($path)) {
            return response()->download($path, null, $headers, null);
        }
        return response()->notFound();
    }
        /**
     * User Information
     * Take 1 parameters(user_id)
     * After Successfull Verification of user it display him his information
     */
    public function logout(LoginAccessRequest $request)
    {
      $connection=new DatabaseConnection();
            $connection->createconnection("users")->updateOne(
                [ '_id' => $request->data->_id],
                [ '$set' => [ 'status' => 0 ,'remember_token' => NULL]]
            );
            return response()->success();       
    }
     /**
     * Forget Password
     * Take 1 parameters($email)
     * It will take email from thr user and make a random token store it in the database and send that token to user on the given mail
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $connection= new DataBaseConnection();
        $token = rand(100,10000);
        $connection->createconnection('users')->updateOne(
            [ 'email'=>$request->email],
            [ '$set' => ['email_token'=>$token]]
        );
        $this->sendVerificationMail($request->email,$token);
        return response()->success();
    }

    public function sendVerificationMail($email,$user_token)
    { 
        $details=[
            'title' => 'Verification Key'. $user_token,
            'body' => 'Plase Enter the Verfication Key to Enter New Password'
        ];
        Mail::to($email)->send(new testmail($details));
        return response()->success();
    }
    /**
     * Set new password
     * Take 3 parameters($email.verification_code,New Password)
     * 
     */

    public function setNewPassword(ChangePasswordRequest $request) 
    {
        $verification_code=(int)$request->verification_code;
        $connection = new DataBaseConnection();
        $password=Hash::make($request->password);
        $data=(array)$connection->createconnection('users')->findOne(
            ['email_token' => $verification_code]
        );
        if($data==null)
        {
            response()->error();
        }else{
        $connection->createconnection('users')->updateOne(
            [ 'email'=> $request->email ],
            [ '$set' => ['password' => $password]]
        );
        return response()->success();
        }
    }
}

