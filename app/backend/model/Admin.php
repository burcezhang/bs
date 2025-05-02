<?php

namespace app\backend\model;

use think\Model;

class Admin extends Model
{
    protected $autoWriteTimestamp = true;
    protected $name = 'admin';
    /**
         * 验证用户登录
         * @param string $username 用户名
         * @param string $password 密码
         * @return array|false
         */
    public static function login($username, $password)
    {
        $user = self::where('username', $username)
            ->where('status', 1)
            ->find();
        if (!$user) {
            return false;
        }
        // 验证密码 (假设密码是经过password_hash加密存储的)
        if (password_verify($password, $user['password'])) {
            return $user->toArray();
        }
        return false;
    }
}
