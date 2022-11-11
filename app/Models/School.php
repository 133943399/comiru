<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function pass(){
        $this->status = 1;
        return $this;
    }

    public function schoolTeacher(){
        return $this->hasOne(SchoolTeacher::class,'sid');
    }

    public function teacher(){
        return $this->hasOne(User::class,'id','tid');
    }
}
