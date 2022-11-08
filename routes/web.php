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
Route::post('register', 'PassportController@register');

Route::group(['middleware' => 'auth:web'], function () {
    //获取菜店
    Route::get('menu/getList', 'PermissionController@index');

    Route::get('student', 'StudentController@list');
    Route::get('teacher', 'TeacherController@list');

    Route::post('push/message', 'PushController@message');

    //退出登录
    Route::post('auth/logout', 'PassportController@logout');

//    Route::get('followme', 'PermissionController@index');
});

Route::get('/line', 'LoginController@pageLine');