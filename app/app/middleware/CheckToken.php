<?php

namespace app\app\middleware;

use think\facade\Cache;
use think\facade\Request;
use think\facade\Log;

class CheckToken
{
    public function handle($request, \Closure $next)
    {
        // 获取当前请求的URL路径
        $path = trim($request->pathinfo(), '/');
        Log::info('当前请求路径: ' . $path);
        
        // 判断是否是登录接口
        if ($path === 'app/login/wxLogin' || $path === 'login/wxLogin') {
            return $next($request);
        }

        // 获取token
        $token = $request->header('Authorization');
        if (empty($token)) {
            return json(['code' => 401, 'msg' => '请先登录', 'data' => null]);
        }

        // 验证token
        $userInfo = Cache::get('token_' . $token);
        if (empty($userInfo)) {
            return json(['code' => 401, 'msg' => '登录已过期，请重新登录', 'data' => null]);
        }

        // 将用户信息注入到请求中
        $request->userInfo = $userInfo;
        
        return $next($request);
    }
} 