<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Validator;
use Datatables;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Currency;
use App\Models\Withdraw;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Classes\GeniusMailer;
use App\Models\Generalsetting;
use App\Models\UserSubscription;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use DB;

class AdminComissionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    //*** GET Request
    public function index()
    {
       $vendor = DB::table('admin_commission')
        ->join('users','users.id','=','admin_commission.vendor_id')
        ->get();

        return view('admin.admin_comission.index',compact('vendor'));
    }

    public function makepaid(Request $request){
        $comission = DB::table('admin_commission')->where('vendor_id','=',$request->id)->first();
        if($request->paid >= $comission->comission){
        $vendor = DB::table('admin_commission')
        ->where('vendor_id','=',$request->id)
        ->update([
        "paid" => 1,
        "comission" => 0
        ]);
        }
        else{
            $vendor = DB::table('admin_commission')
            ->where('vendor_id','=',$request->id)
            ->update([
            "paid" => 0,
            "comission" => $comission->comission - $request->paid
            ]);
        }


        $vendor = DB::table('admin_commission')
        ->join('users','users.id','=','admin_commission.vendor_id')
        ->get();
        return view('admin.admin_comission.index',compact('vendor'));


    }

    public function requestpayment($id,$amount){

        $vendor = DB::table('users')->where('id','=',$id)->first();
        $mobile= $vendor->phone;

      $url = "http://premium.mdlsms.com/smsapi";
      $msg="Please pay ".$amount."BDT of Admin Comission amount. Brac Bank ACC : 1304104667580001 Name : Md S Ahmed বিকাশ থেকে যেভাবে একাউন্টে টাকা পাঠাবেন: আরো > ট্রান্সফার মানি > ব্যাংক একাউন্ট> ব্র্যাক ব্যাংকসিলেক্ট - অন্যের তারপর ব্যাঙ্ক একাউন্ট নাম্বার এবং নাম ";
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
      $vendor = DB::table('vendor_comission')
        ->join('users','users.id','=','vendor_comission.vendor_id')
        ->get();
        return view('admin.vendor_comission.index',compact('vendor'));

    }





}
