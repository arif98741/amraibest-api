<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;

use Auth;

class AuthController extends Controller
{

    /* Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('phone', 'password');



        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the authenticated User
     *
     * @return JsonResponse
     */
    public function profile()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }

    /**
     * Register New User
     */
    public function register(Request $request)
    {
    	 
    	 try{
    	 	$validator = Validator::make($request->all(),[
                        'name' => 'required',
                        'phone' => 'required|unique:users',
                        'address' => 'required',
                        'password' => 'required|min:6|max:20',
                        'confirm_password' => 'required|min:3|max:20|same:password',

                    ]);

    	 	if ($validator->fails()) {

    	 		return response()->json([
    	 			'status' => 'error',
    	 			'code' => 401,
    	 			'message' => 'Registration failed due to error',
    	 			'error' => $validator->errors()
    	 		]);
    	 	}else{
    	 		$userData = $request->all();
    	 		$userData['password'] = bcrypt($userData['password']);
    	 		$data  = User::create($userData);
    	 		return response()->json([

    	 			'status' => 'success',
    	 			'message' => 'Registration Successful',
    	 			'code' => 200,
    	 			'data' => $data
    	 		]);
    	 	}
    

    	 }catch(\Exception $e)
    	 {
    	 	return response()->json([

    	 			'status' => 'error',
    	 			'message' => 'error',
    	 			'code' => 503,
    	 	]);
    	 }

    }

    
}
