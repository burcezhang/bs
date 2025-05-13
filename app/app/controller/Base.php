<?php

namespace app\app\controller;

use app\BaseController;
use think\facade\Cache;
use think\facade\Request;

/**
 * 公共类
 */
class Base extends BaseController
{
    /**
     * 获取用户信息
     * @return array|false
     */
    protected function getUserInfo()
    {
        // 尝试多种方式获取token
        $token = Request::header('token');
        if (empty($token)) {
            $token = Request::header('Authorization');
        }
        if (empty($token)) {
            $token = Request::param('token');
        }
        
        if (empty($token)) {
            return false;
        }
        
        // 如果token以Bearer开头，去掉Bearer
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        
        $userInfo = Cache::get('token_' . $token);
        if (empty($userInfo)) {
            // 尝试从数据库获取用户信息
            $user = \app\backend\model\User::where('token', $token)->find();
            if ($user) {
                $userInfo = [
                    'openid' => $user['openid'],
                    'login_time' => time()
                ];
                // 更新缓存
                Cache::set('token_' . $token, $userInfo, 7 * 24 * 3600);
            }
        }
        
        return $userInfo;
    }
}
