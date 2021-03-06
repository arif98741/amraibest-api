<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Support\Facades\Input;
use Validator;
use Exception;

use Auth;

class BlogController extends Controller
{
    /**
     * BlogController constructor
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    /**
     * Show blogs
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function blogs(Request $request)
    {

        try {

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

            $param = $request->all();
            $order = $param['order'];
            $orderBy = $param['order_by'];

            if ($request->has('per_page') && $request->has('page')) {

                $per_page = $param['per_page'];
                $page = $param['page'];
                $blogs = Blog::with(['category'])
                    //->where( ['category_id' => $category_id])
                    ->orderBy($orderBy, $order)
                    ->paginate($per_page, ['*'], 'page', $page);

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $blogs->items(),
                    'page' => [
                        'current_page' => $blogs->currentPage(),
                        'last_page' => $blogs->lastPage(),
                        'per_page' => $blogs->perPage(),
                        'total' => $blogs->total(),
                    ]
                ]);

            } else {

                $blogs = Blog::with(['category'])
                    ->orderBy($orderBy, $order)
                    ->get();

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $blogs
                ]);
            }
        } catch (\QueryException  $ex) {

            return response()->json([

                'status' => 'error',
                'message' => $ex->getMessage(),
                'code' => 503,
            ]);
        }

    }

    /**
     * Show blogs by category id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function blogsByCategory(Request $request)
    {
        try {

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


            $param = $request->all();
            $order = $param['order'];
            $category_id = $param['category_id'];
            $orderBy = $param['order_by'];

            if ($request->has('per_page') && $request->has('page')) {

                $per_page = $param['per_page'];
                $page = $param['page'];
                $blogs = Blog::with(['category'])
                    ->where(['category_id' => $category_id])
                    ->orderBy($orderBy, $order)
                    ->paginate($per_page, ['*'], 'page', $page);

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $blogs->items(),
                    'page' => [
                        'current_page' => $blogs->currentPage(),
                        'last_page' => $blogs->lastPage(),
                        'per_page' => $blogs->perPage(),
                        'total' => $blogs->total(),
                    ]
                ]);

            } else {

                $blogs = Blog::with(['category'])
                    ->where(['category_id' => $category_id])
                    ->orderBy($orderBy, $order)
                    ->get();

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $blogs
                ]);
            }
        } catch (\QueryException  $ex) {

            return response()->json([

                'status' => 'error',
                'message' => $ex->getMessage(),
                'code' => 503,
            ]);
        }

    }

    /**
     * Single Blog
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function single_blog(Request $request)
    {
        if (!$request->has('blog_id')) {

            return response()->json([

                'status' => 'error',
                'message' => 'blog_id Parameter missing',
                'code' => 201
            ]);
        }

        try {
            $param = $request->all();
            $blog = Blog::with(['category'])
                ->where('id', $param['blog_id'])
                ->firstOrFail();
            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $blog
            ]);
        } catch (Exception $e) {
            return response()->json([

                'status' => 'error',
                'message' => 'no data found',
                'code' => 405,
            ]);
        }

    }

    /**
     * Get Blog Categories
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function blogCategories(Request $request)
    {
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
        try {
            $param = $request->all();
            $orderBy = $param['order_by'];
            $order = $param['order'];
            $categories = BlogCategory::orderBy($orderBy, $order)
                ->get();

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $categories
            ]);
        } catch (Exception $e) {
            return response()->json([

                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => 405,
            ]);
        }
    }

    /**
     * Single Blog
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'category_id' => 'required',
            'title' => 'required|min:3|max:50',
            'details' => 'required|min:3|max:50',
            'meta_tag' => 'required|min:3|max:50',
            'meta_description' => 'required|min:3|max:50',
            'tags' => 'sometimes',
            'photo' => 'mimes:jpeg,jpg,png,svg|required|max:2048',
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

        try {
            $input = $request->all();
            $data = Auth::user();
            if ($file = $request->file('photo')) {

                $name = time() . str_replace(' ', '', $file->getClientOriginalName());
                $status = $file->move('assets/images/blogs', $name);
                $input['photo'] = $name;
            }
            $input['status'] = 0;
            Blog::create($input);
            return response()->json([

                'status' => 'success',
                'message' => 'successfully inserted',
                'code' => 200,
            ]);
        } catch (Exception $e) {
            return response()->json([

                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => 405,
            ]);
        }

    }

}
