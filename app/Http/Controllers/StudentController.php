<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\School;
use App\Models\SchoolStudent;
use App\Models\SchoolTeacher;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function list(){
        $page = request()->input('page', 1);
        $perPage = request()->input('perPage', 20);

        $schools = SchoolTeacher::select('sid')->where(['tid'=>\Auth::user()->id])->get();
        $sid_arr = array_column($schools->toArray(),'sid');
        $students = SchoolStudent::select('stid')->whereIn('sid',$sid_arr)->with('student')->paginate($perPage, ['*'], 'page', $page)->toArray();

        return response()->json([
            'code' => 200,
            'data' =>['list'=>array_column($students['data'],'student')],
            'pagination' =>[
                'count'=>$students['total'],
                'currentPage'=>$students['current_page'],
                'perPage'=>$students["per_page"],
                'total'=>$students['total'],
                'totalPages'=>$students['last_page'],
            ]
        ]);
    }

    public function follow($id)
    {
        $f = Follow::where([
            'sid' => \Auth::user()->id,
            'tid' => $id,
        ])->first();

        if (empty($f)) {
            $follow = new Follow;
            $follow->sid = \Auth::user()->id;
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


        return response()->json([
            'code' => 200,
            'data' => ['message' => $message],
        ]);
    }
}
