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
        $x = [
            'five' => $id,
            'six' => $json
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'data fetched',
            'data' => $packages,
            'code' => 206,
        ]);

    }

    //*** GET Request
    public function index()
    {
        return view('vendor.package.index');
    }

    //*** GET Request
    public function create()
    {
        $sign = Currency::where('is_default', '=', 1)->first();
        return view('vendor.package.create', compact('sign'));
    }

    //*** POST Request
    public function store(Request $request)
    {
        //--- Validation Section
        $rules = ['title' => 'unique:packages'];
        $customs = ['title.unique' => 'This title has already been taken.'];
        $validator = Validator::make(Input::all(), $rules, $customs);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $sign = Currency::where('is_default', '=', 1)->first();
        $data = new Package();
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $input['price'] = ($input['price'] / $sign->value);
        $data->fill($input)->save();
        //--- Logic Section Ends

        //--- Redirect Section        
        $msg = 'New Data Added Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends    
    }

    //*** GET Request
    public function edit($id)
    {
        $sign = Currency::where('is_default', '=', 1)->first();
        $data = Package::findOrFail($id);
        return view('vendor.package.edit', compact('data', 'sign'));
    }

    //*** POST Request
    public function update(Request $request, $id)
    {
        //--- Validation Section
        $rules = ['title' => 'unique:packages,title,' . $id];
        $customs = ['title.unique' => 'This title has already been taken.'];
        $validator = Validator::make(Input::all(), $rules, $customs);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
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