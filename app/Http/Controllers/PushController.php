<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushController extends Controller
{
    //
    public function message(Request $request)
    {
        $pusher = new \Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            ['cluster' => env('PUSHER_APP_CLUSTER'),]
        );

        $a = $pusher->trigger(
            'test-channel',
            'test-event',
            ['text' => $request->msg]
        );

        return response()->json([
            'status' => $a,
        ]);
    }

}
