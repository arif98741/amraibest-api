<?php

namespace App\Http\Controllers\Api\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Product;
use Illuminate\Support\Facades\Input;
use Image;
use Auth;
use Validator;
use Exception;

class GalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    /**
     * Show Gallery Images
     * @return \Illuminate\Http\JsonResponse
     */
    public function show_gallery_images(Request $request)
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

        try {
            $user = Auth::user();
            $products = Product::where('user_id', $user->id)
                ->where('id', $request->product_id)
                ->findOrFail($request->product_id);
            $data = [];
            if (count($products->galleries)) {

                $data = $products->galleries;
            }
            return response()->json([
                'status' => 'success',
                'message' => 'data fetched',
                'code' => 200,
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([

                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => 405,
            ]);
        }
        if (count($prod->galleries)) {
            $data[0] = 1;
            $data[1] = $prod->galleries;
        }
        return response()->json($data);
    }

    public function store(Request $request)
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

        $data = null;
        $product_id = $request->product_id;

        if ($files = $request->file('gallery')) {
            foreach ($files as $key => $file) {
                $val = $file->getClientOriginalExtension();
                if ($val == 'jpeg' || $val == 'jpg' || $val == 'png' || $val == 'svg') {
                    $gallery = new Gallery;

                    $img = Image::make($file->getRealPath())->resize(800, 800);
                    $thumbnail = time() . str_random(8) . '.jpg';
                    $img->save(public_path() . '/assets/images/galleries/' . $thumbnail);

                    $gallery['photo'] = $thumbnail;
                    $gallery['product_id'] = $product_id;
                    $gallery->save();
                    $data[] = $gallery;
                }
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'successfully stored',
            'error' => $validator->getMessageBag()->toArray(),
            'code' => 200
        ]);

    }

    /**
     * Delete Gallery Image
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
        };
        try {
            $galleries = Gallery::where('product_id', $request->product_id)
                ->get();
            if ($galleries->count() == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'data not found',
                    'code' => 405
                ]);
            }
            foreach ($galleries as $gallery) {

                if (file_exists(public_path() . '/assets/images/galleries/' . $gallery->photo)) {
                    unlink(public_path() . '/assets/images/galleries/' . $gallery->photo);
                }
                $gallery->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'gallery images deleted successfully',
                'error' => $validator->getMessageBag()->toArray(),
                'code' => 200
            ]);

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => 405
            ]);

        }

    }
}
