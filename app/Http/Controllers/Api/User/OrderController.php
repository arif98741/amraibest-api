<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderTrack;
use Illuminate\Database\QueryException;
use Validator;
use Exception;

use Auth;

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

        try{

            $products = Order::where('user_id',$this->user_id())
            ->orderBy('id','desc')
            ->get();

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $products
            ]);
            
        }catch(QueryException  $ex){
        
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
    public function single_order(Request $request)
    {

        try{
        
            if (!$request->has('order_id') ) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'order_id Parameter missing',
                    'code' => 201
                ]);
            }

            $param = $request->all();
            $products = Order::where([
                'user_id'=> $this->user_id(),
                'id'=> $param['order_id'],
            ])
            ->orderBy('id','desc')
            ->get();

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $products
            ]);
            
        }catch(QueryException  $ex){
        
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

        try{
        
            if (!$request->has('order_id') ) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'order_id Parameter missing',
                    'code' => 201
                ]);
            }

            if (!$request->has('order_by') ) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'order_by Parameter missing',
                    'code' => 201
                ]);
            }

            if (!$request->has('order') ) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'order Parameter missing',
                    'code' => 201
                ]);
            }

            if (!in_array($request->order_by, array('id','created_id'))) {

                return response()->json([

                    'status' => 'error',
                    'message' => [
                        'order_by parameter not supported. supported parameters: id, created_at'
                    ],
                    'code' => 204
                ]);
            }

            if (!in_array($request->order, array('asc','desc'))) {

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
                'order_id'=> $param['order_id'],
            ])->orderBy($param['order_by'],$param['order'])
            ->get();

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $products
            ]);
            
        }catch(QueryException  $ex){
        
            return response()->json([

                    'status' => 'error',
                    'message' => $ex->getMessage(),
                    'code' => 503,
            ]);
        }

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
