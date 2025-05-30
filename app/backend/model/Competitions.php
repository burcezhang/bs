<?php

namespace app\backend\model;

use think\Model;

class Competitions extends Model
{
    protected $autoWriteTimestamp = true;
    protected $name = 'competitions';
    protected $pk = 'id';

    public function listType($type, $date, $page = 1, $limit = 20)
    {
        $list = self::where('type', $type)
            ->where(function ($query) use ($date) {
                if ($date) {
                    $query->where('start_time', '>=', $date)
                        ->where('start_time', '<=', $date . ' 23:59:59');
                }
            })
            ->page($page, $limit)
            ->order('start_time', 'desc')
            ->select()
            ->toArray();
        $count = self::where('type', $type)
            ->where(function ($query) use ($date) {
                if ($date) {
                    $query->where('start_time', '>=', $date)
                        ->where('start_time', '<=', $date . ' 23:59:59');
                }
            })
            ->field('count(id) as count')
            ->find();
        $res = [
            'list' => $list,
            'count' => $count['count'],
        ];
        return $res;
    }

    public function detail($id)
    {
        $detail = self::find($id);
        return $detail;
    }

    public function listByIds($ids)
    {
        $res = self::where('id', 'in', $ids)
            ->select()
            ->toArray();
        return $res;
    }
}
