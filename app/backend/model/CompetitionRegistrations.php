<?php

namespace app\backend\model;

use think\Model;

class CompetitionRegistrations extends Model
{
    protected $autoWriteTimestamp = true;
    protected $name = 'competition_registrations';
    protected $pk = 'id';

    public function isRegistration($competition_id, $openid)
    {   
        $res = self::where('competition_id', $competition_id)
            ->where('openid', $openid)
            ->where('status', 1)
            ->find();
        return $res;
    }

    public function userList($openid, $type, $page, $limit)
    {   
        $list = self::where('openid', $openid)
            ->where('type', $type)
            ->where('status', 1)
            ->page($page, $limit)
            ->select()
            ->toArray();
        $count = self::where('openid', $openid)
            ->where('type', $type)
            ->where('status', 1)
            ->field('count(id) as count')
            ->find();
        $res = [
            'list' => $list,
            'count' => $count['count'],
        ];
        return $res;
    }
}
