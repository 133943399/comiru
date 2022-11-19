<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('auth/login', 'PassportController@login');
Route::post('auth/register', 'PassportController@register');
Route::get('login/line', 'PassportController@getLineUrl');
Route::get('line/callback', 'PassportController@lineCallBack');

Route::group(['middleware' => 'auth:web'], function () {
    Route::get('/users/getUserInfo', 'UserController@info');//登录用户信息
    Route::get('/menu/getList', 'PermissionController@index');//获取菜店
    Route::get('/bindline', 'PassportController@bindLine');//绑定line账号


    Route::group(['prefix' => 'user'], function () {
        Route::get('/change_list', 'PassportController@getUserRole');
        Route::post('/change', 'PassportController@setUserRole');

        //学生
        Route::get('/students', 'UserController@getStudents');
        Route::post('/students/{id}/follow', 'UserController@follow');

        //教师
        Route::get('/teachers', 'UserController@getTeachers');
        Route::get('/teachers/follow', 'UserController@followList');
        Route::post('/teachers/invent', 'UserController@invent');
    });

    //学校
    Route::group(['prefix' => 'school'], function () {
        Route::get('/', 'SchoolController@list');
        Route::post('/', 'SchoolController@create');
        Route::post('/invite/{id}', 'SchoolController@invite');
        Route::post('/student/{id}', 'SchoolController@student');
    });

    //聊天
    Route::post('/push/message/', 'PushController@message');
    //公告
    Route::post('/push/notice/', 'PushController@notice');

    //退出登录
    Route::post('/auth/logout', 'PassportController@logout');
});

