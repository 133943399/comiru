<?php

namespace App\Http\Controllers;

use App\Models\SchoolUser;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
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

        $user = User::where('email', $request->username)->first();

        if (!\Hash::check($request->password, $user->password)) {
            $this->setMsg(400, '用户不存在 或 密码错误');
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
            $this->setMsg(400, $validator->messages()->first());
            return $this->responseJSON();
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        if (!isset($request->sid)) {
            $input['type'] = 1;//学校管理员
        } else {
            $input['type'] = 3;//普通教师
        }
        $input['line_id'] = '';
        $user = User::create($input);

        if (isset($request->sid)) {
            //邀请 绑定学校 普通教师角色
            $schoolUser = new SchoolUser();
            $schoolUser->sid = $request->sid;
            $schoolUser->uid = $user->id;
            $schoolUser->type = 2;
        }

        \Log::info('user', $user->toArray());
        $tokenResult = $user->createToken('web');

        $this->setData([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ]);
        return $this->responseJSON();
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
     * 从line获取用户信息登录.
     */
    public function lineCallBack()
    {
        $auth_user = Auth::user();
        $line_user = Socialite::driver('line')->stateless()->user();
        if (!empty($auth_user)) {
            if (!empty($auth_user->line_id)) {
                $this->setMsg(400, '已经绑定Line用户');
                return $this->responseJSON();
            }

            if ($auth_user->type != 2) {
                $user = User::where(['line_id' => $line_user->id, 'type' => 1])->first();
                if (!empty($user)) {
                    $this->setMsg(400, "Line已经绑定过其他教师");
                    return $this->responseJSON();
                }
            }

            //绑定逻辑
            $user = User::find($auth_user->id);
            $user->line_id = $line_user->id;
            $user->save();

            $this->setMsg(200, '绑定成功');
            return $this->responseJSON();
        } else {
            //登录逻辑
            $user = User::where([
                ['line_id', '=', $line_user->id],
            ])->first();

            if (empty($user)) {
                $this->setMsg(400, '用户不存在');
            }

            $tokenResult = $user->createToken('web');
            $token = $tokenResult->token;
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

    }

    /**
     * 用户多角色设置
     *
     * @return JsonResponse
     */
    public function getUserRole()
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('perPage', 20);

        $lineId = Auth::user()->line_id;
        $users = User::where(['line_id' => $lineId])->paginate($perPage, ['*'], 'page', $page);

        $data = [
            'list'       => $users->items(),
            'pagination' => [
                'total'       => $users->total(),
                'count'       => $users->count(),
                'perPage'     => $users->perPage(),
                'currentPage' => $users->currentPage(),
                'totalPages'  => $users->lastPage(),
            ],
        ];
        $this->setData($data);
        return $this->responseJSON();
    }

    /**
     * 用户多角色设置
     *
     * @return JsonResponse
     */
    public function setUserRole()
    {
        $uid = intval(request()->input('id'));

        $lineId = Auth::user()->line_id;

        $user = User::find($uid);
        if ($lineId != $user->line_id) {
            $this->setMsg(400, '无法切换');
            return $this->responseJSON();
        }

        $tokenResult = $user->createToken('web');
        $token = $tokenResult->token;
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
}