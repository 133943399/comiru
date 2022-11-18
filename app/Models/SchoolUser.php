<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolUser extends Model
{
    public $timestamps = false;

    public function school()
    {
        return $this->hasOne(School::class, 'id', 'sid');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'uid');
    }

    static function data(array $where = [], int $page = 1, int $perPage = 20)
    {
        $page = request()->input('page', $page);
        $perPage = request()->input('perPage', $perPage);

        $query = self::select('*');

        if (!empty($where)) {
            $query->where($where);
        }

        $query->with('school','user');
        $query->orderBy('sid', 'asc');
        $res = $query->paginate($perPage, ['*'], 'page', $page);
        foreach ($res->items() as &$v){
            $v->follow = 0;
            $f = Follow::where([
                'sid' => \Auth::id(),
                'tid' => $v->uid,
            ])->exists();

            if ($f){
                $v->follow = 1;
            }
        }
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

    static function stData(array $whereIn = [], int $page = 1, int $perPage = 20)
    {
        $page = request()->input('page', $page);
        $perPage = request()->input('perPage', $perPage);

        $query = self::select('*');

        if (!empty($whereIn)) {
            $query->whereIn(...$whereIn);
        }
        $query->where(['type'=>0]);
        $query->with('school','user');
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
