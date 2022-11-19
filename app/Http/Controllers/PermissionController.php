<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $user = \Auth::user();
        $type = $user->type;
        $menu = [
            'code'    => 200,
            'message' => '操作成功',
        ];
        if ($type == 1) {
            //管理员
            $menu['data'] = [
                ["title" => "学生列表", "icon" => "", "path" => "/system/student", "component" => "/system/student", "target" => "_self"],
                ["title" => "关注我的学生", "icon" => "", "path" => "/system/followme", "component" => "/system/followme", "target" => "_self"],
                ["title" => "学校列表", "icon" => "", "path" => "/system/school", "component" => "/system/school", "target" => "_self"],
                ["title" => "聊天室", "icon" => "", "path" => "/system/chat", "component" => "/system/chat", "target" => "_self"],
                ["title" => "通知", "icon" => "", "path" => "/system/notice", "component" => "/system/notice", "target" => "_self"],
                ["title" => "绑定line", "icon" => "", "path" => "/system/line", "component" => "/system/line", "target" => "_self"],
            ];
        } elseif ($type == 3) {
            //普通教师
            $menu['data'] = [
                ["title" => "查看学生", "icon" => "", "path" => "/system/student", "component" => "/user/students", "target" => "_self"],
                ["title" => "关注列表", "icon" => "", "path" => "/system/followme", "component" => "/system/followme", "target" => "_self"],
                ["title" => "通知", "icon" => "", "path" => "/system/notice", "component" => "/system/notice", "target" => "_self"],
                ["title" => "绑定line", "icon" => "", "path" => "/system/line", "component" => "/system/line", "target" => "_self"],
            ];
        } else {
            //student
            $menu['data'] = [
                ["title" => "查看老师", "icon" => "", "path" => "/system/teacher", "component" => "/system/teacher", "target" => "_self"],
                ["title" => "聊天室", "icon" => "", "path" => "/system/chat", "component" => "/system/chat", "target" => "_self"],
                ["title" => "通知", "icon" => "", "path" => "/system/notice", "component" => "/system/notice", "target" => "_self"],
                ["title" => "绑定line", "icon" => "", "path" => "/system/line", "component" => "/system/line", "target" => "_self"],
            ];
        }

        if (!empty($user->line_id)) {
            $count = User::where(['line_id' => $user->line_id])->get()->count();
            if ($count > 0) {
                $menu['data'][] = ["title" => "切换用户", "icon" => "", "path" => "/system/change", "component" => "/system/change", "target" => "_self"];
            }
        }

        return response()->json($menu);

    }
}
