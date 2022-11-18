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

    public function teacher()
    {
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

        $query->with('student','teacher');
        $query->orderBy('sid', 'asc');
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
