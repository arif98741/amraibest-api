<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Childcategory;
use App\Models\Currency;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Attribute;
use App\Models\AttributeOption;
use Auth;
use DB;
use Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Image;
use Session;
use Validator;

class ProductController extends Controller
{
    public $global_language;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    /**
     * Product Data Tables
     * @return JsonResponse
     */
    public function datatables()
    {
        try {
            $user = Auth::user();

            $products = $user->products()->where('product_type', 'normal')->orderBy('id', 'desc')->get();
            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $products
            ]);
        } catch (Exception $ex) {
            return response()->json([

                'status' => 'error',
                'message' => $ex->getMessage(),
                'code' => 503,
            ]);
        }

    }

    /**
     * Get Single Pro
     * @return JsonResponse
     */
    public function single_product(Request $request)
    {
        $rules = [
            'product_id' => 'required',
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

        try {
            $user = Auth::user();

            $products = $user->products()
                ->where(
                    [
                        'user_id' => $user->id,
                        'id' => $param['product_id']
                    ]
                )->first();

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $products
            ]);
        } catch (Exception $ex) {
            return response()->json([

                'status' => 'error',
                'message' => $ex->getMessage(),
                'code' => 503,
            ]);
        }

    }

    /*
     * Product Catelogs
     */
    public function catalogdatatables()
    {

        try {
            $user = Auth::user();

            $products = Product::where('product_type', 'normal')
                ->where('status', '=', 1)
                ->where('user_id', '=', $user->id)
                ->where('is_catalog', '=', 1)
                ->orderBy('id', 'desc')->get();

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $products
            ]);
        } catch (Exception $ex) {
            return response()->json([

                'status' => 'error',
                'message' => $ex->getMessage(),
                'code' => 503,
            ]);
        }

    }


    //*** GET Request
    public function status($id1, $id2)
    {
        $data = Product::findOrFail($id1);
        $data->status = $id2;
        $data->update();
    }


    /**
     * Delete Product
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        $rules = [
            'product_id' => 'required',
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
        $product_id = $request->product_id;
        $user = Auth::user();
        try {
            $data = Product::where([
                'id' => $product_id,
                'user_id' => $user->id
            ])->findOrFail($product_id);
            return $data;

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation error. parameter missing',
                'error' => $e->getMessage(),
                'code' => 405
            ]);
        }

        if ($data->galleries->count() > 0) {
            foreach ($data->galleries as $gal) {
                if (file_exists(public_path() . '/assets/images/galleries/' . $gal->photo)) {
                    unlink(public_path() . '/assets/images/galleries/' . $gal->photo);
                }
                $gal->delete();
            }

        }

        if ($data->ratings->count() > 0) {
            foreach ($data->ratings as $gal) {
                $gal->delete();
            }
        }
        if ($data->wishlists->count() > 0) {
            foreach ($data->wishlists as $gal) {
                $gal->delete();
            }
        }
        if ($data->clicks->count() > 0) {
            foreach ($data->clicks as $gal) {
                $gal->delete();
            }
        }
        if ($data->comments->count() > 0) {
            foreach ($data->comments as $gal) {
                if ($gal->replies->count() > 0) {
                    foreach ($gal->replies as $key) {
                        $key->delete();
                    }
                }
                $gal->delete();
            }
        }

        if (!filter_var($data->photo, FILTER_VALIDATE_URL)) {
            if (file_exists(public_path() . '/assets/images/products/' . $data->photo)) {
                unlink(public_path() . '/assets/images/products/' . $data->photo);
            }
        }

        if (file_exists(public_path() . '/assets/images/thumbnails/' . $data->thumbnail) && $data->thumbnail != "") {
            unlink(public_path() . '/assets/images/thumbnails/' . $data->thumbnail);
        }
        if ($data->file != null) {
            if (file_exists(public_path() . '/assets/files/' . $data->file)) {
                unlink(public_path() . '/assets/files/' . $data->file);
            }
        }
        $data->delete();
        //--- Redirect Section
        $msg = 'Product Deleted Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    public function getAttributes(Request $request)
    {
        $model = '';
        if ($request->type == 'category') {
            $model = 'App\Models\Category';
        } elseif ($request->type == 'subcategory') {
            $model = 'App\Models\Subcategory';
        } elseif ($request->type == 'childcategory') {
            $model = 'App\Models\Childcategory';
        }

        $attributes = Attribute::where('attributable_id', $request->id)->where('attributable_type', $model)->get();
        $attrOptions = [];
        foreach ($attributes as $key => $attribute) {
            $options = AttributeOption::where('attribute_id', $attribute->id)->get();
            $attrOptions[] = ['attribute' => $attribute, 'options' => $options];
        }
        return response()->json($attrOptions);
    }
}
