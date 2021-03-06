<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Vendor;
use App\Models\Order;
use App\Models\OrderTrack;
use App\Models\VendorOrder;
use Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;

class OrderController extends Controller
{

    /* Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }


    /*
    Show Products
    */
    public function orders(Request $request)
    {
        try {

            $orders = VendorOrder::where('user_id', '=', $this->user_id())
                ->orderBy('id', 'desc')
                ->get()
                ->groupBy('order_number');

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $orders
            ]);

        } catch (QueryException  $e) {

            return response()->json([

                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => 503,
            ]);
        }

    }

    /*
    Show Single Order
    */
    public function single_order(Request $request)
    {

        try {

            $rules = [
                'order_number' => 'required',
            ];

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'validation error',
                    'error' => $validator->getMessageBag()->toArray(),
                    'code' => 206
                ]);
            }

            $param = $request->all();

            $order = VendorOrder::where('order_number', '=', $param['order_number'])
                ->where('user_id', $this->user_id())
                ->first();
            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $order
            ]);

        } catch (QueryException  $ex) {

            return response()->json([

                'status' => 'error',
                'message' => $ex->getMessage(),
                'code' => 503,
            ]);
        }

    }

    /*
   Show Single Order
   */
    public function change_status(Request $request)
    {

        try {

            $rules = [
                'order_number' => 'required',
            ];

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'validation error. parameter missing',
                    'error' => $validator->getMessageBag()->toArray(),
                    'code' => 206
                ]);
            }

            $param = $request->all();

            $mainOrder = VendorOrder::with('order')
                ->where('order_number', '=', $param['order_number'])
                ->first();

            if ($mainOrder->status == "completed") {

                return response()->json([
                    'status' => 'info',
                    'message' => 'status already completed',
                    'code' => 207
                ]);

            } else {
                if (isset($mainOrder->order)) {
                    $msg = 'Your order Id ' . $mainOrder->order->order_number . ' status is : ' . $param['status'];
                    // $this->send_sms($mainOrder->order->customer_phone, $msg);
                }

                $user_id = $this->user_id();
                VendorOrder::where('order_number', '=', $param['order_number'])
                    ->where('user_id', '=', $user_id)
                    ->update(['status' => $param['status']]);
                if ($param['status'] == 'declined') {
                    $order_comission = DB::table('orders')->where('order_number', '=', $param['order_number'])->first();
                    $t = $order_comission->pay_amount;
                    $s = $order_comission->shipping_cost;
                    $w = $order_comission->wallet_price;
                    $v = $order_comission->coupon_discount;
                    $comission = ((($t + $w + $v) - $s) / 100) * 10;
                    $ad = $v + $w;
                    $vendorCommission = DB::table('vendor_comission')->where('vendor_id', '=', $user_id)->first();
                    DB::table('vendor_comission')->where('vendor_id', '=', $user_id)->update([
                        "comission" => $vendorCommission->comission - $comission
                    ]);
                    $admin_comission = DB::table('admin_commission')->where('vendor_id', '=', $user_id)->first();
                    DB::table('admin_commission')->where('vendor_id', '=', $user_id)->update([
                        "comission" => $admin_comission->comission - $ad
                    ]);
                } elseif ($param['status'] == 'pending') {
                    $order_comission = DB::table('orders')->where('order_number', '=', $param['order_number'])->first();
                    $t = $order_comission->pay_amount;
                    $s = $order_comission->shipping_cost;
                    $w = $order_comission->wallet_price;
                    $v = $order_comission->coupon_discount;
                    $comission = ((($t + $w + $v) - $s) / 100) * 10;
                    $ad = $v + $w;
                    $vendorCommission = DB::table('vendor_comission')->where('vendor_id', '=', $user_id)->first();
                    DB::table('vendor_comission')->where('vendor_id', '=', $user_id)->update([
                        "comission" => $vendorCommission->comission + $comission
                    ]);
                    $admin_comission = DB::table('admin_commission')->where('vendor_id', '=', $user_id)->first();
                    DB::table('admin_commission')->where('vendor_id', '=', $this->user_id())->update([
                        "comission" => $admin_comission->comission + $ad
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'order status changed successfully',
                    'code' => 200
                ]);

            }

        } catch (QueryException  $ex) {

            return response()->json([

                'status' => 'error',
                'message' => $ex->getMessage(),
                'code' => 503,
            ]);
        }

    }

    /*
    Show Single Order Tracks
    */
    public function order_tracks(Request $request)
    {

        try {

            if (!$request->has('order_id')) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'order_id Parameter missing',
                    'code' => 201
                ]);
            }

            if (!$request->has('order_by')) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'order_by Parameter missing',
                    'code' => 201
                ]);
            }

            if (!$request->has('order')) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'order Parameter missing',
                    'code' => 201
                ]);
            }

            if (!in_array($request->order_by, array('id', 'created_id'))) {

                return response()->json([

                    'status' => 'error',
                    'message' => [
                        'order_by parameter not supported. supported parameters: id, created_at'
                    ],
                    'code' => 204
                ]);
            }

            if (!in_array($request->order, array('asc', 'desc'))) {

                return response()->json([

                    'status' => 'error',
                    'message' => [
                        'order parameter not supported. supported parameters: asc, desc'
                    ],
                    'code' => 204
                ]);
            }


            $param = $request->all();
            $products = OrderTrack::where([
                'order_id' => $param['order_id'],
            ])->orderBy($param['order_by'], $param['order'])
                ->get();

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $products
            ]);

        } catch (QueryException  $ex) {

            return response()->json([

                'status' => 'error',
                'message' => $ex->getMessage(),
                'code' => 503,
            ]);
        }

    }

    public function emailsub(Request $request)
    {


        $gs = Generalsetting::findOrFail(1);
        if ($gs->is_smtp == 1) {
            $data = 0;
            $datas = [
                'to' => $request->to,
                'subject' => $request->subject,
                'body' => $request->message,
            ];

            $mailer = new GeniusMailer();
            $mail = $mailer->sendCustomMail($datas);
            if ($mail) {
                $data = 1;
            }
        } else {
            $data = 0;
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: " . $gs->from_name . "<" . $gs->from_email . ">";
            $mail = mail($request->to, $request->subject, $request->message, $headers);
            if ($mail) {
                $data = 1;
            }
        }

        $msg = $request->message;
        $this->send_sms($request->to, $msg);

        return response()->json($data);
    }

    public function send_sms($mobile, $otp)
    {
        $url = "http://premium.mdlsms.com/smsapi";
        $msg = $otp;
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

    public function user()
    {
        return Auth::guard()->user();
    }

    private function user_id()
    {
        return $this->user()->id;
    }

}
