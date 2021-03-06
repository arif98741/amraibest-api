<?php

namespace App\Http\Controllers\Api\Extra;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Exception;

use Auth;

class OtherController extends Controller
{
    /**
     * OtherController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    /**
     * Show countries
     * @param Request $request
     * @return JsonResponse
     */
    public function countries(Request $request)
    {
        try {

            $countries = DB::table('countries')
                ->orderBy('country_name')
                ->get();

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $countries
            ]);

        } catch (\QueryException  $ex) {

            return response()->json([

                'status' => 'error',
                'message' => $ex->getMessage(),
                'code' => 503,
            ]);
        }

    }

    /**
     * Show currencies
     * @param Request $request
     * @return JsonResponse
     */
    public function currencies(Request $request)
    {

        try {

            $currencies = Currency::orderBy('name', 'asc')
                ->get();

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $currencies
            ]);

        } catch (\QueryException  $ex) {

            return response()->json([

                'status' => 'error',
                'message' => $ex->getMessage(),
                'code' => 503,
            ]);
        }

    }


    /**
     * Show coupons
     * @param Request $request
     * @return JsonResponse
     */
    public function coupons(Request $request)
    {
        try {
            $currencies = Coupon::orderBy('id', 'desc')
                ->get();

            return response()->json([

                'status' => 'success',
                'message' => 'Data Fetched',
                'code' => 200,
                'data' => $currencies
            ]);

        } catch (\QueryException  $ex) {

            return response()->json([

                'status' => 'error',
                'message' => $ex->getMessage(),
                'code' => 503,
            ]);
        }

    }

}
