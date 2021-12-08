<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\testmail;

class MailController extends Controller
{
    public function sendmail()
    { 
        $details=[
            'title'=>'Hi',
             'body'=>'mail'
        ];
        Mail::to("m.usamayounas669@gmail.com")->send(new testmail($details));
        return "email send";
    }
}
