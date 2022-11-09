<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolStudent extends Model
{
    public $timestamps = false;

    public function student()
    {
        return $this->hasOne(User::class,'id','stid');
    }
}
