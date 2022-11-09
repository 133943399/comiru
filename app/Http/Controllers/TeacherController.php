<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\SchoolStudent;
use App\Models\SchoolTeacher;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function list()
    {
        $page = request()->input('page',1);
        $perPage = request()->input('perPage',20);

        $id = \Auth::user()->id;
        $school = SchoolStudent::where(['stid'=>$id])->first();
        if (empty($school)){
            return response()->json([
                'code'       => 200,
                'data'       => ['list' => []],
                'pagination' => [
                ],
            ]);
        }
        $teacher_ids = SchoolTeacher::select("tid")->where(['sid'=>$school->sid])->get()->toArray();
        $teacher_ids = array_column($teacher_ids,'tid');
        $teachers = User::whereIn('id', $teacher_ids)->paginate($perPage, ['*'], 'page', $page);

        $data = $teachers->toArray();
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

    public function followme()
    {
        $page = request()->input('page',1);
        $perPage = request()->input('perPage',20);

        $students = Follow::where('tid', '=', \Auth::user()->id)->with('student')->paginate($perPage, ['*'], 'page', $page);

        $data = $students->toArray();

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
}
