<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class PassportController extends Controller
{

    /**
     * login api
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {

        $request->validate([
            'username'    => 'required|string|email',
            'password'    => 'required|string',
            'remember_me' => 'boolean',
        ]);

        $user = User::where('email', $request->username)->firstOrFail();
        if (!\Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Unauthorized！',
            ], 401);
        }

        $tokenResult = $user->createToken('web');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);

        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ]);

    }

    /**
     * Register api
     *
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['type'] = 1;
        $input['line_id'] = '';

        $user = User::create($input);

        \Log::info('user',$user->toArray());
        $tokenResult = $user->createToken('web');

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ]);
    }

    public function logout()
    {
        return response()->json();
    }
}