<?php

namespace App\Http\Controllers;

use App\Exceptions\Code;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //
    private $code    = 200;
    private $message = '操作成功';
    private $op_code = 200;
    private $data    = [];

    public function setMsg($code = 200, $message = '')
    {
        $this->code = $code;
        $this->message = $message;
    }

    public function setOpCode($op_code = 200)
    {
        $this->op_code = $op_code;
    }

    public function setData($data = [])
    {
        $this->data = $data;
    }

    public function responseJSON($code = 200)
    {
        $arrayResult['code'] = $this->code ?? 200;
        $arrayResult['message'] = $this->message;
        $arrayResult['op_code'] = $this->op_code;
        $arrayResult['data'] = $this->data;

        return response()->json($arrayResult, $code)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function success($message = '操作成功')
    {
        $this->code = 200;
        $this->message = $message;

        return $this->responseJSON();
    }

    public function error($message = '操作失败')
    {
        $this->code = 400;
        $this->message = $message;
        return $this->responseJSON();
    }
}
