<?php

namespace app\service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Config;

class JwtService
{
    // 秘钥
    private static $key;

    // 初始化
    private static function init()
    {
        self::$key = Config::get('jwt.key', 'default_key');
    }

    /**
     * 生成JWT token
     * @param array $data 用户数据
     * @param int $expire 过期时间(秒)
     * @return string
     */
    public static function createToken(array $data, int $expire = 7200): string
    {
        self::init();
        $time = time();
        $token = [
            'iat' => $time,                      // 签发时间
            'nbf' => $time,                      // 生效时间
            'exp' => $time + $expire,            // 过期时间
            'data' => $data                     // 自定义数据
        ];
        return JWT::encode($token, self::$key, 'HS256');
    }

    /**
     * 验证JWT token
     * @param string $token
     * @return array|false
     */
    public static function verifyToken(string $token)
    {
        self::init();
        try {
            // 通常token以Bearer开头，需要去掉
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }
            $decoded = JWT::decode($token, new Key(self::$key, 'HS256'));
            return (array)$decoded->data;
        } catch (\Exception $e) {
            return false;
        }
    }
}
