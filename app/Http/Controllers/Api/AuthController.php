<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    use \Backpack\CRUD\app\Library\Auth\ThrottlesLogins;

    public function login(Request $request)
    {

        $credentials = $request->only('personal_phone', 'password');
//        if($this->guard()->attempt($this->credentials($request))){
        if(Auth::attempt($credentials)){

            $token = \Illuminate\Support\Str::random(60);
            $this->guard()->user()->forceFill([
                'api_token' => $token,
            ])->save();

            return response()->json([
                'status' => 'ok',
                'token' => $token
            ], 200);
        }


        return response()->json([
            'status' => 'error'
        ], 401);
    }


    public function logout(Request $request) {
        $this->guard()->user()->forceFill([
            'api_token' => '',
        ])->save();
        return response()->json([
            'status' => 'ok',
        ], 204);
    }


	public function ping()
    {
        return response()->json([
            'status' => 'ok',
            'token' => auth()->user()->api_token
        ], 200);
    }

    public function unauthorized(Request $request)
    {
        return response('Unauthorized', 401);
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    public function username()
    {
        return 'email';
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    protected function guard()
    {
        return Auth::guard();
    }

    protected function preSync()
    {
        $clientsHash = app('\App\Http\Controllers\Api\ClientController')->sync();
        if(!empty($clientsHash)){
            return response()->json([
                'status' => 'ok',
                'clientsHash' => $clientsHash
            ], 200);
        }
    }
}
