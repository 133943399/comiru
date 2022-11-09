<?php

namespace App\Admin\Actions\School;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Pass extends RowAction
{
    public $name = '审批通过';

    public function handle(Model $model)
    {
        $model->pass()->save();
        return $this->response()->success('Success message.')->refresh();
    }

}