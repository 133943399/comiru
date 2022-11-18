<?php

namespace App\Http\Controllers;

use App\Mail\InviteTeacher;
use App\Models\Follow;
use App\Models\SchoolUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function info()
    {
        $userInfo = User::find(Auth::id())->toArray();

        $this->setData($userInfo);
        return $this->responseJSON();
    }

    /**
     * 老师
     * @return JsonResponse
     */
    public function getTeachers()
    {
        $studentInfo = SchoolUser::select('sid')->where(['uid' => Auth::id()])->first();
        $teachersInfo = SchoolUser::data([
            ['sid', '=', $studentInfo->sid],
            ['type', '>', 0],
        ]);
        $this->setData($teachersInfo);
        return $this->responseJSON();
    }

    /**
     * @return JsonResponse
     */
    public function followList()
    {
        $list = Follow::data(['tid' => Auth::id()]);
        $this->setData($list);
        return $this->responseJSON();
    }


    /**
     * 学生
     * @return JsonResponse
     */
    public function getStudents()
    {

        $schoolIds = SchoolUser::where(['uid' => Auth::id()])->get()->pluck('sid');
        $userInfo = SchoolUser::stData(['sid',$schoolIds]);

        $this->setData($userInfo);
        return $this->responseJSON();
    }

    public function follow($id)
    {
        $f = Follow::where([
            'sid' => \Auth::user()->id,
            'tid' => $id,
        ])->first();

        if (empty($f)) {
            $follow = new Follow;
            $follow->sid = \Auth::id();
            $follow->tid = $id;
            $follow->save();
            $message = "关注成功";
        } else {
            $f->where([
                'sid' => \Auth::user()->id,
                'tid' => $id,
            ])->delete();
            $message = "取消关注";
        }

        $this->setMsg(200, $message);
        return $this->responseJSON();
    }
}
