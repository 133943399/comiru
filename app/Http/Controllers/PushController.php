<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use Illuminate\Http\Request;

class PushController extends Controller
{
    //
    public function message(Request $request)
    {
        $msg = $request->msg;
        $user_id = $request->user_id;

        event(new MessageEvent($user_id, $msg));
        return response()->json([
            'status' => $msg,
        ]);
    }

}
