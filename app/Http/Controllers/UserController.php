<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function info()
    {
        $userInfo = User::find(Auth::id())->toArray();

        return response()->json([
            'code'       => 200,
            'data'       => $userInfo,
        ]);
    }
}
