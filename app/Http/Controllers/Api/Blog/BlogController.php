<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Generalsetting;
use Illuminate\Database\QueryException;
use Validator;
use Exception;

use Auth;

class BlogController extends Controller
{

   
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    /**
    Sub Categories
    */
    public function blogs(Request $request)
    {
        
        try{
            $param = $request->all();
            
            if (!$request->has('category_id') ) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'category_id Parameter missing',
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

            if (!$request->has('order_by') ) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'order_by Parameter missing',
                    'code' => 201
                ]);
            }


            $order = $param['order'];
            $orderBy = $param['order_by'];
            $category_id = $param['category_id'];

            if ($request->has('per_page') && $request->has('page')) {

                $per_page = $param['per_page'];
                $page = $param['page'];
                $sub_categories = Subcategory::with(['category'])
                ->where( ['category_id' => $category_id])
                ->orderBy($orderBy,$order)
                ->paginate($per_page, ['*'], 'page', $page);

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $sub_categories->items(),
                    'page' => [
                        'current_page' => $sub_categories->currentPage(),
                        'last_page' => $sub_categories->lastPage(),
                        'per_page' => $sub_categories->perPage(),
                        'total' => $sub_categories->total(),
                    ]
                ]);

            }else{
                
                $sub_categories = Subcategory::with(['category'])
                ->orderBy($orderBy,$order)
                ->get();

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $sub_categories
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
