<?php

namespace App\Http\Controllers;

use App\Models\SchoolUser;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Validator;

class PassportController extends Controller
{
    public function index()
    {
        return view('login');
    }

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

        $user = User::where('email', $request->username)->first();

        if (!\Hash::check($request->password, $user->password)) {
            $this->setMsg(400,'用户不存在 或 密码错误');
            return $this->responseJSON();
        }

        $tokenResult = $user->createToken('web');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);

        $token->save();

        $this->setData([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ]);
        return $this->responseJSON();

    }

    /**
     * Register api
     *
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed',
            'sid'      => 'int',
        ]);

        if ($validator->fails()) {
            $this->setMsg(400,$validator->messages()->first());
            return $this->responseJSON();
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['type'] = 1;
        $input['line_id'] = '';
        $user = User::create($input);

        if (isset($request->sid)){
            //邀请 绑定学校 普通教师角色
            $schoolUser = new SchoolUser();
            $schoolUser->sid = $request->sid;
            $schoolUser->uid = $user->id;
            $schoolUser->type = 2;
        }

        \Log::info('user', $user->toArray());
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

    /**
     * 将用户重定向到line认证页面
     */
    public function getLineUrl()
    {
        $oauth_url = Socialite::driver('line')->redirect()->getTargetUrl();
        return response()->json([
            'url' => $oauth_url,
        ]);
    }

    /**
     * 从line获取用户信息.
     */
    public function lineCallBack()
    {
        $auth_user = Socialite::driver('line')->stateless()->user();
        $user = User::where(['line_id' => $auth_user->id])->first();
        if (empty($user)) {
            $user = User::create([
                'email'    => $auth_user->email ?? 'demo@comiru.com',
                'name'     => $auth_user->name,
                'password' => bcrypt($auth_user->name),
                'type'     => 1,
                'line_id'  => $auth_user->id,
            ]);
        }

        $tokenResult = $user->createToken('web');
        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ]);
    }
}