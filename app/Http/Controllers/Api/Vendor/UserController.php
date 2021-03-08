<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Input;
use Validator;
use Exception;
use Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function store(Request $request)
    {

        try {

            if (!$request->has('product_id')) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'product_id Parameter missing',
                    'code' => 201
                ]);
            }

            $param = $request->all();

            try {

                $products = Wishlist::where([
                    'user_id' => $this->user_id(),
                    'product_id' => $param['product_id']
                ])->get();

                if ($products->count() > 0) {
                    return response()->json([

                        'status' => 'error',
                        'message' => 'Already Exist',
                        'code' => 202,
                    ]);
                } else {

                    $wish = new Wishlist;
                    $wish->user_id = $this->user_id();
                    $wish->product_id = $param['product_id'];
                    $wish->save();
                    return response()->json([

                        'status' => 'success',
                        'message' => 'Insert successful',
                        'code' => 200,
                    ]);
                }


            } catch (Exception $e) {

                return response()->json([

                    'status' => 'error',
                    'message' => $e->getMessage(),
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

    public function delete(Request $request, $id)
    {
        try {

            $wish = Wishlist::where('user_id', $this->user_id())
                ->find($id);

            if ($wish) {

                $wish->delete();
                return response()->json([

                    'status' => 'success',
                    'message' => 'Delete successful',
                    'code' => 200,
                ]);
            } else {
                return response()->json([

                    'status' => 'error',
                    'message' => 'Failed to delete',
                    'code' => 203,
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

    public function wishlist(Request $request)
    {

        try {

            $param = $request->all();

            try {
                $products = Wishlist::with(['product'])
                    ->where(['user_id' => $this->user_id()])
                    ->get();

                return response()->json([

                    'status' => 'success',
                    'message' => 'Data Fetched',
                    'code' => 200,
                    'data' => $products
                ]);
            } catch (Exception $e) {
                return response()->json([

                    'status' => 'error',
                    'message' => 'No data found',
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
     * Update User Profile
     */
    public function update_profile(Request $request)
    {
        try {
            // dd($request->all());
            $rules = [
                'photo' => 'mimes:jpeg,jpg,png,svg|required|max:2048',
                'name' => 'min:3|max:100',
                'address' => 'required|min:3|max:50',
                'city' => 'required|min:3|max:50',
                'country' => 'required|min:3|max:50',
                'state' => 'required|min:3|max:50',
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

            $input = $request->all();
            $data = Auth::user();
            if ($file = $request->file('photo')) {
                $name = time() . str_replace(' ', '', $file->getClientOriginalName());
                $file->move('assets/images/users/', $name);
                if ($data->photo != null) {
                    if (file_exists(public_path() . '/assets/images/users/' . $data->photo)) {
                        unlink(public_path() . '/assets/images/users/' . $data->photo);
                    }
                }
                $input['photo'] = $name;
            }

            if (!$request->has('name')) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'name Parameter missing',
                    'code' => 201
                ]);
            }

            if (!$request->has('phone')) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'phone Parameter missing',
                    'code' => 201
                ]);
            }

            if (!$request->has('zip')) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'zip Parameter missing',
                    'code' => 201
                ]);
            }


            if (!$request->has('city')) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'city Parameter missing',
                    'code' => 201
                ]);
            }


            if (!$request->has('country')) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'country Parameter missing',
                    'code' => 201
                ]);
            }

            if (!$request->has('state')) {

                return response()->json([

                    'status' => 'error',
                    'message' => 'state Parameter missing',
                    'code' => 201
                ]);
            }

            $status = User::where('id', $this->user_id())
                ->update($input);
            if ($status) {
                return response()->json([

                    'status' => 'success',
                    'message' => 'update successful',
                    'code' => 200
                ]);
            } else {
                return response()->json([

                    'status' => 'error',
                    'message' => 'update failed',
                    'code' => 205
                ]);
            }

        } catch (Exception $ex) {
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
