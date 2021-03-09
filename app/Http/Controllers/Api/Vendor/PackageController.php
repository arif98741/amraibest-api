<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Package;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
use Auth;
use Session;
use DB;

class PackageController extends Controller
{
    public $global_language;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function packageList()
    {

        $packages = Package::where('user_id', Auth::user()->id)->get();
        if ($packages->count() == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'no data found',
                'code' => 405,
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'data fetched',
            'code' => 200,
            'data' => $packages,
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|unique:packages',
            'subtitle' => 'required',
            'price' => 'required',
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

        $sign = Currency::where('is_default', '=', 1)->first();
        $data = new Package();
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $input['price'] = ($input['price'] / $sign->value);

        if ($data->fill($input)->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'successfully saved',
                'error' => $validator->getMessageBag()->toArray(),
                'code' => 200
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'save failed',
                'code' => 200
            ]);
        }

    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function update(Request $request, $id)
    {
        //--- Validation Section
        $rules = ['title' => 'unique:packages,title,' . $id];
        $customs = ['title.unique' => 'This title has already been taken.'];
        $validator = Validator::make(Input::all(), $rules, $customs);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation error',
                'error' => $validator->getMessageBag()->toArray(),
                'code' => 206
            ]);
        }
        return 'hi';

        $sign = Currency::where('is_default', '=', 1)->first();
        $data = Package::findOrFail($id);
        $input = $request->all();
        $input['price'] = ($input['price'] / $sign->value);
        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section     
        $msg = 'Data Updated Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends            
    }

    //*** GET Request Delete
    public function destroy($id)
    {
        $data = Package::findOrFail($id);
        $data->delete();
        //--- Redirect Section     
        $msg = 'Data Deleted Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends     
    }
}