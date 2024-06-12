<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function Pest\Laravel\json;

class AdminLoginController extends ApiController
{
    /**
     * @return string
     */
    private function username()
    {
        return 'email';
    }

    /**
     * Login api
     * @return \Illuminate\Http\Response
     */

    public function login(Request $request)
    {
        $remember = $request->remember ?? false;
        $validator = Validator::make($request->all(), [
            $this->username() => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendFormErrors($validator->errors());
        }

        if (Auth::attempt($validator->valid())) {
            $user = Auth::user();
            $response['token'] = $user->createToken('appToken')->accessToken;
            return $this->sendResponse($response, 'User login successfully.');
        }

        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }
}
