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

    public function regList($competition_id, $type, $page, $limit)
    {   
        $list = self::alias('c')
            ->field('c.id, u.nickname, u.sex, u.level_id')
            ->where('c.competition_id', $competition_id)
            ->where('c.type', $type)
            ->where('c.status', 1)
            ->leftJoin('user u', 'u.openid = c.openid')
            ->page($page, $limit)
            ->select()
            ->toArray();
        $count = self::alias('c')
            ->field('count(c.id) as count')
            ->where('c.competition_id', $competition_id)
            ->where('c.type', $type)
            ->where('c.status', 1)
            ->leftJoin('user u', 'u.openid = c.openid')
            ->find();
        $res = [
            'list' => $list,
            'count' => $count['count'],
        ];
        return $res;
    }

    public function regCancel($id)
    {   
        $res = self::where('id', $id)
            ->save(['status' => 2]);
        return $res;
    }

}
