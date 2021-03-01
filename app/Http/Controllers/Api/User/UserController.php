<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\QueryException;
use Validator;
use Exception;
use Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function wishlist()
    {
        try{
            if (!$request->has('user_id') ) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'user_id Parameter missing',
                    'code' => 201
                ]);
            }

            $param = $request->all();

            try{
                $products = Product::with(['category','subcategory'])
                ->findOrFail($param['product_id']);

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $products
                ]);
            }catch(Exception $e)
            {
                return response()->json([

                    'status' => 'error',
                    'message' => 'No result found',
                    'code' => 405,
                ]);
            }
                            
        }catch(QueryException  $ex){
        
            return response()->json([

                    'status' => 'error',
                    'message' => $ex->getMessage(),
                    'code' => 503,
            ]);
        }
    }
 
}
