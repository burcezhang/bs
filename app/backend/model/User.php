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
}
