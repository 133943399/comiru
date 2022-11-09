<?php

namespace App\Http\Controllers;

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
            if ($user->pid == 0) {
                //管理员
                $menu['data'] = [["id" => 1, "pid" => 0, "title" => "学校管理员", "icon" => "", "path" => "", "component" => "", "target" => "_self", "children" => [
                    ["id" => 2, "pid" => 1, "title" => "学生列表", "icon" => "", "path" => "/system/student", "component" => "/system/student", "target" => "_self"],
                    ["id" => 3, "pid" => 1, "title" => "关注我的学生", "icon" => "", "path" => "/system/followme", "component" => "/system/followme", "target" => "_self"],
                    ["id" => 3, "pid" => 1, "title" => "学校列表", "icon" => "", "path" => "/system/school", "component" => "/system/school", "target" => "_self"],
                ]]];
            } else {
                //普通教师
                $menu['data'] = [["id" => 1, "pid" => 0, "title" => "普通老师", "icon" => "", "path" => "", "component" => "", "target" => "_self", "children" => [
                    ["id" => 2, "pid" => 1, "title" => "查看学生", "icon" => "", "path" => "/system/student", "component" => "/system/student", "target" => "_self"],
                    ["id" => 3, "pid" => 1, "title" => "关注列表", "icon" => "", "path" => "/system/followme", "component" => "/system/followme", "target" => "_self"],
                ]]];
            }

        } else {
            //student
            $menu['data'] = [["id" => 1, "pid" => 0, "title" => "学生", "icon" => "el-icon-setting", "path" => "", "component" => "", "target" => "_self", "children" => [
                [ "id"=>2,"pid"=>1,"title"=>"查看老师","icon"=>"","path"=>"/system/teacher","component"=>"/system/teacher","target"=>"_self"],
            ]]];
        }
        return response()->json($menu);

    }
}
