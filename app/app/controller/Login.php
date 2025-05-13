<?php

namespace app\app\controller;

use think\Request;
use think\facade\Config;
use think\facade\Cache;
use app\backend\model\User as UserModel;

class Login extends Base
{
    /**
     * 微信小程序登录
     * @return array
     */
    public function wxLogin(Request $request)
    {
        // 尝试多种方式获取code
        $code = $request->param('code');
        if (empty($code)) {
            $code = $request->post('code');
        }
        if (empty($code)) {
            $jsonData = json_decode(file_get_contents('php://input'), true);
            $code = $jsonData['code'] ?? '';
        }
        
        if (empty($code)) {
            $this->error('code不能为空');
        }

        // 获取小程序配置
        $appid = Config::get('wx.appid');
        $secret = Config::get('wx.secret');
        
        // 请求微信接口获取openid和session_key
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";
        $response = file_get_contents($url);
        $result = json_decode($response, true);

        if (isset($result['errcode'])) {
            $this->error('微信登录失败：' . $result['errmsg']);
        }

        // 生成token
        $token = md5($result['openid'] . time() . rand(1000, 9999));
        
        // 将用户信息存入缓存
        $userInfo = [
            'openid' => $result['openid'],
            'session_key' => $result['session_key'],
            'login_time' => time()
        ];
        Cache::set('token_' . $token, $userInfo, 7 * 24 * 3600); // 缓存7天

        // 查询用户是否存在
        $user = UserModel::where('openid', $result['openid'])->find();
        
        if ($user) {
            // 用户存在，更新token
            UserModel::where('openid', $result['openid'])->update([
                'token' => $token,
                'update_time' => time()
            ]);
        } else {
            // 用户不存在，创建新用户
            UserModel::create([
                'openid' => $result['openid'],
                'token' => $token,
                'create_time' => time(),
                'update_time' => time()
            ]);
        }

        // 返回token和用户信息
        $data = [
            'token' => $token,
            'openid' => $result['openid']
        ];

        // 如果用户存在且有额外信息，则添加到返回数据中
        if ($user) {
            if (!empty($user['headimg'])) {
                $data['headimg'] = $user['headimg'];
            }
            if (!empty($user['nickname'])) {
                $data['nickname'] = $user['nickname'];
            }
            if (!empty($user['phone'])) {
                $data['phone'] = $user['phone'];
            }
            // 添加角色信息
            $data['role'] = $user['role'] ?? 0;
        }

        $this->success('登录成功', $data);
    }
} 