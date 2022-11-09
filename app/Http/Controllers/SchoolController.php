<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\School;
use App\Models\SchoolStudent;
use App\Models\SchoolTeacher;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class SchoolController extends Controller
{
    public function list()
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('perPage', 20);

        $school = School::paginate($perPage, ['*'], 'page', $page);
        $data = $school->toArray();
        return response()->json([
            'code'       => 200,
            'data'       => ['list' => $data['data']],
            'pagination' => [
                'count'       => $data['total'],
                'currentPage' => $data['current_page'],
                'perPage'     => $data["per_page"],
                'total'       => $data['total'],
                'totalPages'  => $data['last_page'],
            ],
        ]);
    }

    public function create(Request $request)
    {
        $tid = \Auth::user()->id;

        $school = new School();
        $school->tid = $tid;
        $school->name = $request->name;

        if ($school->save()) {
            $code = 200;
            $messag = '操作成功';
        } else {
            $code = 400;
            $messag = '操作失败';
        }


        $st = SchoolTeacher::where([
            'sid' => $school->id,
            'tid' => $tid,
        ])->first();

        if (empty($st)) {
            $stc = new SchoolTeacher;
            $stc->sid = $school->id;
            $stc->tid = $tid;
            $stc->save();
        }

        return response()->json([
            'code' => $code,
            'data' => ['message' => $messag],
        ]);
    }

    public function invite(Request $request, $id)
    {
        $teacher = User::where([
            'email' => $request->email,
            'type'  => 1,
        ])->first();
        if (!empty($teacher)) {
            $st = SchoolTeacher::where([
                'sid' => $id,
                'tid' => $teacher->id,
            ])->first();

            if (empty($st)) {
                $stc = new SchoolTeacher;
                $stc->sid = $id;
                $stc->tid = $teacher->id;
                $stc->save();
            }
            $messag = '邀请成功';
        } else {
            $messag = '教师不存在';
        }

        return response()->json([
            'code' => 200,
            'data' => ['message' => $messag],
        ]);
    }

    public function student(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            's_name'   => 'required',
            's_email'  => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $input['name'] = $input['s_name'];
        $input['email'] = $input['s_email'];
        $input['password'] = bcrypt($input['password']);
        $input['type'] = 2;
        $input['line_id'] = '';

        $user = User::create($input);

        $s_st = new SchoolStudent();
        $s_st->sid = $id;
        $s_st->stid = $user->id;
        $s_st->save();

        return response()->json();
    }

}
