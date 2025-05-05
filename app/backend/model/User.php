<?php

namespace app\backend\model;

use think\Model;

class User extends Model
{
    protected $autoWriteTimestamp = true;
    protected $name = 'user';
    protected $pk = 'id';

    /**
     * 验证用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return array|false
    */
    public static function info($openid)
    {
        $user = self::where('openid', $openid)
            ->field('id, phone, nickname, headimg, level_id')
            ->find();
        return $user ? $user->toArray() : [];
    }

    /**
     * 验证用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return array|false
    */
    public function scoreList($club_id, $page, $limit)
    {
        $list = self::field('nickname, sex, level_id, score')
            ->order('score', 'desc')
            ->page($page, $limit)
            ->select()
            ->toArray();
        $count = self::where('score >= 0')
            ->field('count(id) as count')
            ->find();
        $res = [
            'list' => $list,
            'count' => $count['count'] ?? 0,
        ];
        return $res;
    }
}
