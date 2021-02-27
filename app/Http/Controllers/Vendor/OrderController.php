<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\VendorOrder;
use DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('emailsub');
    }

    public function index()
    {
        $user = Auth::user();
        $orders = VendorOrder::where('user_id','=',$user->id)->orderBy('id','desc')->get()->groupBy('order_number');
        return view('vendor.order.index',compact('user','orders'));
    }

    public function show($slug)
    {
        $user = Auth::user();
        $order = Order::where('order_number','=',$slug)->first();
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        $shipping_cost = Shipping::where('user_id','=',Auth::user()->id)->first();
        return view('vendor.order.details',compact('user','order','cart','shipping_cost'));
    }

    public function license(Request $request, $slug)
    {
        $order = Order::where('order_number','=',$slug)->first();
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        $cart->items[$request->license_key]['license'] = $request->license;
        $order->cart = utf8_encode(bzcompress(serialize($cart), 9));
        $order->update();
        $msg = 'Successfully Changed The License Key.';
        return response()->json($msg);
    }



    public function invoice($slug)
    {
        $user = Auth::user();
        $order = Order::where('order_number','=',$slug)->first();
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        $shipping_cost = Shipping::where('user_id','=',Auth::user()->id)->first();
        return view('vendor.order.invoice',compact('user','order','cart','shipping_cost'));
    }

    public function printpage($slug)
    {
        $user = Auth::user();
        $order = Order::where('order_number','=',$slug)->first();
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('vendor.order.print',compact('user','order','cart'));
    }

    public function status($slug,$status)
    {
        $mainorder = VendorOrder::with('order')->where('order_number','=',$slug)->first();
        if ($mainorder->status == "completed"){
            return redirect()->back()->with('success','This Order is Already Completed');
        }else{
            if(isset($mainorder->order)){
         $msg='Your order Id '.$mainorder->order->order_number.' status is : '. $status;
            $this->send_sms($mainorder->order->customer_phone,$msg);
            }

        $user = Auth::user();
        $order = VendorOrder::where('order_number','=',$slug)->where('user_id','=',$user->id)->update(['status' => $status]);
        if($status == 'declined'){
            $order_comission = DB::table('orders')->where('order_number','=',$slug)->first();
             $t = $order_comission->pay_amount;
             $s = $order_comission->shipping_cost;
             $w = $order_comission->wallet_price;
             $v = $order_comission->coupon_discount;
            $comission= ((($t+$w +$v)-$s)/100)*10;
            $ad = $v + $w;
            $vendor_comission = DB::table('vendor_comission')->where('vendor_id','=',Auth::user()->id)->first();
            $vendor = DB::table('vendor_comission')->where('vendor_id','=',Auth::user()->id)->update([
                "comission" => $vendor_comission->comission - $comission
            ]);
            $admin_comission = DB::table('admin_commission')->where('vendor_id','=',Auth::user()->id)->first();
            $admin = DB::table('admin_commission')->where('vendor_id','=',Auth::user()->id)->update([
                "comission" => $admin_comission->comission - $ad
            ]);
        }elseif($status == 'pending'){
            $order_comission = DB::table('orders')->where('order_number','=',$slug)->first();
             $t = $order_comission->pay_amount;
             $s = $order_comission->shipping_cost;
             $w = $order_comission->wallet_price;
             $v = $order_comission->coupon_discount;
            $comission= ((($t+$w +$v)-$s)/100)*10;
            $ad = $v + $w;
            $vendor_comission = DB::table('vendor_comission')->where('vendor_id','=',Auth::user()->id)->first();
            $vendor = DB::table('vendor_comission')->where('vendor_id','=',Auth::user()->id)->update([
                "comission" => $vendor_comission->comission + $comission
            ]);
            $admin_comission = DB::table('admin_commission')->where('vendor_id','=',Auth::user()->id)->first();
            $admin = DB::table('admin_commission')->where('vendor_id','=',Auth::user()->id)->update([
                "comission" => $admin_comission->comission + $ad
            ]);
        }
        return redirect()->route('vendor-order-index')->with('success','Order Status Updated Successfully');
    }
    }

       public function emailsub(Request $request)
    {


        $gs = Generalsetting::findOrFail(1);
        if($gs->is_smtp == 1)
        {
            $data = 0;
            $datas = [
                    'to' => $request->to,
                    'subject' => $request->subject,
                    'body' => $request->message,
            ];

            $mailer = new GeniusMailer();
            $mail = $mailer->sendCustomMail($datas);
            if($mail) {
                $data = 1;
            }
        }
        else
        {
            $data = 0;
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
            $mail = mail($request->to,$request->subject,$request->message,$headers);
            if($mail) {
                $data = 1;
            }
        }

        $msg=$request->message;
            $this->send_sms($request->to,$msg);

        return response()->json($data);
    }

       public function send_sms($mobile,$otp) {
      $url = "http://premium.mdlsms.com/smsapi";
      $msg=$otp;
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

}
