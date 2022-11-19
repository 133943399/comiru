<?php

namespace App\Http\Controllers;

use App\Mail\InviteTeacher;
use App\Models\School;
use App\Models\SchoolUser;
use App\Models\SchoolTeacher;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class SchoolController extends Controller
{
    public function list()
    {
        $uid = \Auth::user()->id;
        $school = SchoolUser::data([
            ['uid', '=', $uid],
        ]);
        $this->setData($school);
        return $this->responseJSON();
    }

    public function create(Request $request)
    {
        $uid = \Auth::user()->id;

        $school = new School();
        $school->uid = $uid;
        $school->name = $request->name;

        if ($school->save()) {
            $code = 200;
            $message = '操作成功';
            $this->setMsg($code, $message);

        } else {
            $code = 400;
            $message = '操作失败';
            $this->setMsg($code, $message);
        }

        $stc = new SchoolUser;
        $stc->sid = $school->id;
        $stc->uid = $uid;
        $stc->type = 1;
        $stc->save();

        return $this->responseJSON();
    }

    public function invite(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $this->setMsg(400, $validator->messages()->first());
            return $this->responseJSON();
        }

        $user = User::where([
            'email' => $request->email,
            'type'  => 1,
        ])->first();
        if (!empty($user)) {
            $this->setMsg(400, '无法邀请管理员用户');
            return $this->responseJSON();
        }

        $user = User::where([
            ['email', '=', $request->email],
            ['type', '<>', 1],
        ])->first();

        $teacher = [];
        if (!empty($user)) {
            $teacher = SchoolUser::where([
                'sid' => $id,
                'uid' => $user->id,
            ])->first();
        }

        if (empty($teacher)) {
            $school = School::find($id);
            \Mail::send(new InviteTeacher($school, $request->email));

            $st = SchoolTeacher::where([
                'sid' => $id,
                'tid' => $teacher->id,
            ])->first();

            if (empty($st)) {
                $stc = new SchoolUser();
                $stc->sid = $id;
                $stc->uid = $teacher->id;
                $stc->type = 2;//0学生,1管理员,2普通老师
                $stc->save();
            }
            $messag = '邀请已发送';
        } else {
            $messag = '该邮箱已被邀请';
        }
        $this->setMsg(200, $messag);
        return $this->responseJSON();
    }

    public function student(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            's_name'   => 'required',
            's_email'  => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->setMsg(400, $validator->messages()->first());
            return $this->responseJSON();
        }

        $v = User::where('email', $request->s_email)->first();
        if (!empty($v)) {
            $this->setMsg(400, '邮箱以被注册');
            return $this->responseJSON();
        }


        $input = $request->all();
        $input['name'] = $input['s_name'];
        $input['email'] = $input['s_email'];
        $input['password'] = bcrypt($input['password']);
        $input['type'] = 2;
        $input['line_id'] = '';

        $user = User::create($input);

        $s_st = new SchoolUser();
        $s_st->sid = $id;
        $s_st->uid = $user->id;
        $s_st->save();

        return $this->responseJSON();
    }

}
