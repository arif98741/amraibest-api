<?php

namespace App\Http\Controllers\User;

use App\Models\Generalsetting;
use App\Models\User;
use Illuminate\Http\Request;
use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;

class ForgotController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showForgotForm()
    {
      return view('user.forgot');
    }
    
         public function send_sms($mobile,$otp) {
      $url = "http://premium.mdlsms.com/smsapi";
      $msg=$otp." .Please,don't share with anyone.";
      $data = [
        "api_key" => "C20006315fca5e1a5edbd4.21877943",
        "type" => "text",
        "contacts" => $mobile,
        "senderid" => "8809612441118",
        "msg" => $msg
      ];
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $response = curl_exec($ch);
      curl_close($ch);
      return $response;
    }

    public function forgot(Request $request)
    {
      $gs = Generalsetting::findOrFail(1);
      $input =  $request->all();
      if (User::where('phone', '=', $request->email)->count() > 0) {
          
       if($request->otp){
                $check=\App\Models\OTP::where('mobile',$request->email)->where('otp',$request->otp)->latest()->first();
                if(!isset($check)){
                    return response()->json(array('errors' => ['otp'=>"OTP doesn't Match !!!"]));
                    

                }else{
                if(strtotime(now())-strtotime($check->created_at) > 120 ){

                     return response()->json(array('errors' => ['otp'=>"OTP Validate time over. Resend Again !!!"]));
                
                }
                }
            }
            
            
      // user found
      $admin = User::where('phone', '=', $request->email)->firstOrFail();
      $autopass = str_random(8);
      $input['password'] = bcrypt($autopass);
      $admin->update($input);
      $subject = "Reset Password Request";
      $msg = "Your New Password is : ".$autopass;
     
        $status= $this->send_sms($request->email,$msg);
        
        
        
      if($gs->is_smtp == 1)
      {
          $data = [
                  'to' => $request->email,
                  'subject' => $subject,
                  'body' => $msg,
          ];

          $mailer = new GeniusMailer();
          $mailer->sendCustomMail($data);                
      }
      else
      {	    $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
          $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
          mail($request->email,$subject,$msg,$headers);            
      }
      return response()->json('Your Password Reseted Successfully. Please Check your mobile for new Password.');
      }
      else{
      // user not found
      return response()->json(array('errors' => [ 0 => 'No Account Found With This Mobile No.' ]));    
      }  
    }

}
