<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type', 'status', 'line_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 获得与用户关联学校
     */
    public function school()
    {
        return $this->hasOne(School::class, 'uid');
    }

    public function followSt()
    {
        return $this->hasOne(Follow::class, 'tid')->whereSid(\Auth::id());
    }

    static function data(array $where = [], array $ids, int $page = 1, int $perPage = 20)
    {
        $page = request()->input('page', $page);
        $perPage = request()->input('perPage', $perPage);

        $query = self::select('*');

        if (!empty($where)) {
            $query->where($where);
            if ($where['type'] == 1){
                $query->with('followSt');
            }
        }

        $query->whereIn('id', $ids);
        $query->orderBy('id', 'asc');
        $res = $query->paginate($perPage, ['*'], 'page', $page);
        $resArr = $res->toArray();
        $list = $resArr['data'];
        foreach ($list as $key=>$item){
            if (!empty($item['follow_st'])) {
                $list[$key]['follow'] = 1;
            }else{
                $list[$key]['follow'] = 0;
            }
        }
        return [
            'list'       => $list,
            'pagination' => [
                'total'       => $res->total(),
                'count'       => $res->count(),
                'perPage'     => $res->perPage(),
                'currentPage' => $res->currentPage(),
                'totalPages'  => $res->lastPage(),
            ],
        ];
    }
}
