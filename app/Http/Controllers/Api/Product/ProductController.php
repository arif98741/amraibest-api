<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Childcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Rating;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Input;
use Validator;
use Exception;
use Auth;

class ProductController extends Controller
{
    /**
     * ProductController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }


    /**
     * Show Products
     * @param Request $request
     * @return JsonResponse
     */
    public function products(Request $request)
    {

        try {

            $rules = [
                'order' => 'required',
                'order_by' => 'required',
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
            $order = $param['order'];
            $orderBy = $param['order_by'];

            if ($request->has('per_page') && $request->has('page')) {

                $per_page = $param['per_page'];
                $page = $param['page'];
                $products = Product::with(['category', 'subcategory', 'galleries'])
                    ->orderBy($orderBy, $order)
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

            } else {

                $products = Product::with(['category', 'subcategory', 'galleries', 'user', 'ratings'])
                    ->orderBy($orderBy, $order)
                    ->get();

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $products
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

    /**
     * Show single product
     * @param Request $request
     * @return JsonResponse
     */
    public function single_product(Request $request)
    {

        try {
            $rules = [
                'product_id' => 'required',
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

            try {
                $products = Product::with(['category', 'subcategory', 'galleries', 'user', 'ratings'])
                    ->findOrFail($param['product_id']);

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $products
                ]);
            } catch (Exception $e) {
                return response()->json([

                    'status' => 'error',
                    'message' => 'No result found',
                    'code' => 405,
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

    /**
     * Average Ratings of Single Product
     * 100% calculation
     * @param Request $request
     * @return JsonResponse
     */
    public function ratings(Request $request)
    {

        try {
            $rules = [
                'order' => 'product_id',
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
            $products = Rating::ratings($param['product_id']);

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

    /**
     * Ratings of Single Product
     * @param Request $request
     * @return JsonResponse
     */
    public function rating(Request $request)
    {

        try {
            $rules = [
                'product_id' => 'required',
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
            $products = Rating::rating($param['product_id']);

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

    /**
     * Categories
     * @param Request $request
     * @return JsonResponse
     */
    public function categories(Request $request)
    {

        try {
            $rules = [
                'order' => 'required',
                'order_by' => 'required',
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

            $order = $param['order'];
            $orderBy = $param['order_by'];

            if ($request->has('per_page') && $request->has('page')) {

                $per_page = $param['per_page'];
                $page = $param['page'];
                $categories = Category::orderBy($orderBy, $order)
                    ->paginate($per_page, [' * '], 'page', $page);

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

            } else {

                $categories = Category::orderBy($orderBy, $order)
                    ->get();

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $categories
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


    /**
     * Sub Categories
     */
    public function sub_categories(Request $request)
    {

        try {
            $param = $request->all();

            if (!$request->has('category_id')) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'category_id Parameter missing',
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

            if (!$request->has('order_by')) {

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
                    ->where(['category_id' => $category_id])
                    ->orderBy($orderBy, $order)
                    ->paginate($per_page, [' * '], 'page', $page);

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

            } else {

                $sub_categories = Subcategory::with(['category'])
                    ->orderBy($orderBy, $order)
                    ->get();

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $sub_categories
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

    /**
     * Child Categories
     */
    public function child_categories(Request $request)
    {

        try {
            $rules = [
                'subcategory_id' => 'required',
                'order' => 'required',
                'order_by' => 'required',
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
            $order = $param['order'];
            $orderBy = $param['order_by'];
            $subcategory_id = $param['subcategory_id'];

            $child_categories = Childcategory::where(['subcategory_id' => $subcategory_id])
                ->orderBy($orderBy, $order)
                ->get();

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $child_categories
            ]);


        } catch (QueryException  $e) {

            return response()->json([

                'status' => 'error',
                'message' => $e->getMessage(),
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
