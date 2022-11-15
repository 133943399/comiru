<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

class PushController extends Controller
{
    //
    public function message(Request $request)
    {
        $msg = $request->msg;
        $user_id = $request->user_id;

//        event(new MessageEvent($user_id, $msg));
//        return response()->json([
//            'status' => $msg,
//        ]);
        try {


            $pusher = new \Pusher\Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                ['cluster' => env('PUSHER_APP_CLUSTER'),]
            );


            $a = $pusher->trigger('message', 'send_to_user_' . $user_id, [
                'date'    => date('Y-m-d H:i:s', time()),
                'text'    => $msg,
                'mine'    => false,
                'name'    => \Auth::user()->name,
                'user_id' => \Auth::user()->toArray(),
            ]);
        }catch (\Exception $e){
            $a = $e->getMessage();
        }
        return response()->json([
            'status' => $a,
        ]);
    }

}
