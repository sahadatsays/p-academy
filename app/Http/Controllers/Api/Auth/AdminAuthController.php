<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function Pest\Laravel\json;

class AdminAuthController extends ApiController
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
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.'
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();

        return response()->json([
            'userData' => new UserResource($user),
            'accessToken' => $tokenResult->accessToken,
            'tokenType' => 'Bearer',
            'expiresAt' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Revoke current access token
        $accessToken = $user->token();
        $accessToken->revoke();
        return $this->sendResponse([], 'Successfully logged out');
    }

    public function getProfile()
    {
        $user = User::find(auth()->id());

        if ($user == null) {
            return $this->sendError('User has not found!');
        }

        return new UserResource($user);
    }

    /**
     * Profile update
     */
    public function updateProfile(Request $request)
    {
        $user = User::find(auth()->id());
    
        $rules = [
            'username'      => 'required|string',
            'email'         => 'required|email|string',
            'firstName'    => 'required|string',
            'lastName'    => 'required|string',
            'groups'      => 'required|array'
        ];

        if ($request->password) {
            $rules['password'] = 'required|min:6|confirmed';
        }

        $validate = $request->validate($rules);

        $data = [
            'first_name' => $validate['firstName'],
            'last_name' => $validate['lastName'],
            'email' => $validate['email'],
            'username' => $validate['username'],
        ];

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);
        $user->groups()->sync($validate['groups']);

        return $this->sendResponse($user, 'Profile has been updated!');
    }
}
