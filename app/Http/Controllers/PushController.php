<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Events\NoticeEvent;
use Illuminate\Http\Request;

class PushController extends Controller
{
    //
    public function message(Request $request)
    {
        $msg = $request->msg;
        $user_id = $request->user_id;

        $this->setMsg(400,'发送失败');

        if (!empty($user_id)){
            event(new MessageEvent($user_id, $msg));
            $this->setMsg(200,'发送成功');
        }

        return $this->responseJSON();

//        $pusher = new \Pusher\Pusher(
//            env('PUSHER_APP_KEY'),
//            env('PUSHER_APP_SECRET'),
//            env('PUSHER_APP_ID'),
//            ['cluster' => env('PUSHER_APP_CLUSTER'),]
//        );
//
//
////        $a = $pusher->trigger('user_' . $user_id, 'message', [
//        $res = $pusher->trigger('user_'.$user_id, 'message', [
//            'date'    => date('Y-m-d H:i:s', time()),
//            'text'    => ['text'=> $msg],
//            'mine'    => false,
//            'name'    => \Auth::user()->name,
//        ]);
//
//        return response()->json([
//            'status' => $res,
//        ]);
    }

    public function notice(Request $request)
    {
        $msg = $request->msg;
        $this->setMsg(400,'发送失败');

        if (!empty($msg)) {
            event(new NoticeEvent($msg));
            $this->setMsg(200, '发送成功');
        }

        return $this->responseJSON();
    }
}
