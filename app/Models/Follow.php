<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    public $timestamps = false;

    /**
     * 获得与用户关联学校
     */
    public function student()
    {
        return $this->hasOne(User::class,'id','sid');
    }
}
