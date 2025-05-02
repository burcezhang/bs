<?php

namespace app\backend\controller;

use app\BaseController;
use think\facade\Request;
use app\service\JwtService;

/**
 * 公共类
 */
class Base extends BaseController
{
    private $tokenMap = [
        '/backend/admin/login'
    ];
    protected $userData;
    /**
     * @todo 初始加载
     */
    public function __construct()
    {
        header("Content-Type: text/html; charset=utf-8");
        timeLimit();
        // 获取方法url  这里需要校验token 除了login接口不需要校验
        $url = Request::url();
        if (!in_array($url, $this->tokenMap)) {
            $token = Request::header('Authorization');
            $userData = JwtService::verifyToken($token);
            if(!$userData){
                $this->error('用户token错误', [], 901);
            }
            $this->userData = $userData;
        }
    }

}
