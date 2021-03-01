<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Generalsetting;
use Illuminate\Database\QueryException;
use Validator;
use Exception;

use Auth;

class ProductController extends Controller
{

    /* Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['categories']]);
    }


    /*
    Show Products
    */
    public function products(Request $request)
    {

        try{
        
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

            $param = $request->all();

            $order = $param['order'];
            $orderBy = $param['order_by'];

            if ($request->has('per_page') && $request->has('page')) {

                $per_page = $param['per_page'];
                $page = $param['page'];
                $products = Product::with(['category','subcategory','galleries'])
                ->orderBy($orderBy,$order)
                ->paginate($per_page, ['*'], 'page', $page);

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $products->items(),
                    'page' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                    ]
                ]);

            }else{
                
                $products = Product::with(['category','subcategory','galleries','user','ratings'])
                ->orderBy($orderBy,$order)
                ->get();

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $products
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

    /*
    Show Products
    */
    public function single_product(Request $request)
    {

        try{
            if (!$request->has('product_id') ) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'product_id Parameter missing',
                    'code' => 201
                ]);
            }

            $param = $request->all();

            try{
                $products = Product::with(['category','subcategory','galleries','user','ratings'])
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


    /**
    Categories
    */
    public function categories(Request $request)
    {
        
        try{
            $param = $request->all();
            
            if (!$request->has('order') ) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'Order Parameter missing',
                    'code' => 201
                ]);
            }

            if (!$request->has('order_by') ) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'Orderby Parameter missing',
                    'code' => 201
                ]);
            }


            $order = $param['order'];
            $orderBy = $param['order_by'];

            if ($request->has('per_page') && $request->has('page')) {
                
                $per_page = $param['per_page'];
                $page = $param['page'];
                $categories = Category::orderBy($orderBy,$order)
                ->paginate($per_page, ['*'], 'page', $page);

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $categories->items(),
                    'page' => [
                        'current_page' => $categories->currentPage(),
                        'last_page' => $categories->lastPage(),
                        'per_page' => $categories->perPage(),
                        'total' => $categories->total(),
                    ]
                ]);

            }else{
                
                $categories = Category::orderBy($orderBy,$order)
                ->get();

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $categories
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


    /**
    Sub Categories
    */
    public function sub_categories(Request $request)
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
