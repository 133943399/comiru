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

Route::get('auth/login', 'PassportController@index');
Route::post('auth/login', 'PassportController@login');
Route::post('auth/register', 'PassportController@register');

Route::group(['middleware' => 'auth:web'], function () {

    Route::get('/users/getUserInfo', 'UserController@info');


    //获取菜店
    Route::get('/menu/getList', 'PermissionController@index');

    //学生
    Route::group(['prefix' => 'student'], function () {
        Route::get('/', 'StudentController@list');
        Route::post('/{id}/follow', 'StudentController@follow');
    });

    //教师
    Route::group(['prefix' => 'teacher'], function () {
        Route::get('/', 'TeacherController@list');
        Route::get('/followme', 'TeacherController@followme');
        Route::post('/invent', 'TeacherController@invent');
    });

    //学校
    Route::group(['prefix' => 'school'], function () {
        Route::get('/', 'SchoolController@list');
        Route::post('/', 'SchoolController@create');
        Route::put('/invite/{id}', 'SchoolController@invite');
        Route::put('/student/{id}', 'SchoolController@student');
    });

    Route::post('/push/message/', 'PushController@message');

    //退出登录
    Route::post('/auth/logout', 'PassportController@logout');
});

Route::get('login/line', 'PassportController@getLineUrl');
Route::get('login/line/callback', 'PassportController@lineCallBack');