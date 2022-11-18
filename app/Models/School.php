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

    public function teacher(){
        return $this->hasOne(SchoolTeacher::class,'sid');
    }

    public function student(){
        return $this->hasOne(User::class,'id','tid');
    }

    static function data(array $where = [], int $page = 1, int $perPage = 20)
    {
        $page = request()->input('page', $page);
        $perPage = request()->input('perPage', $perPage);

        $query = self::select('*');

        if (!empty($where)) {
            $query->where($where);
        }

        $query->orderBy('id', 'asc');
        $res = $query->paginate($perPage, ['*'], 'page', $page);

        return  [
            'list' => $res->items(),
            'pagination' => [
                'total'       => $res->total(),
                'count'       => $res->count(),
                'perPage'     => $res->perPage(),
                'currentPage' => $res->currentPage(),
                'totalPages'  => $res->lastPage(),
            ]
        ];
    }
}
