<?php

namespace app\backend\controller;

use app\backend\model\Admin as AdminModel;
use app\service\JwtService;
use think\Response;

class Admin extends Base
{
    /**
     * 用户登录
     * @return Response
     */
    public function login()
    {
        try {
            $username = input('username');
            $password = input('password');
            if (empty($username) || empty($password)) {
                $this->error('用户名和密码不能为空', [], 401);
            }
            $user = AdminModel::login($username, $password);
            if (!$user) {
                $this->error('用户名或密码错误', [], 402);
            }
            $userData = [
                'id' => $user['id'],
                'username' => $user['username'],
                'type' => $user['type'] ?? 1,
                'status' => $user['status'] ?? ''
            ];
            $token = JwtService::createToken($userData);
            $res = [
                'code' => 200,
                'msg' => '登录成功',
                'data' => [
                    'token' => $token,
                    'user' => $userData
                ]
            ];
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), [], $e->getCode());
        }

        $this->success('ok', $res);
    }

    /**
     * 获取当前用户信息
     * @return Response
     */
    public function info()
    {
        $this->success('ok', $this->userData);
    }
}
