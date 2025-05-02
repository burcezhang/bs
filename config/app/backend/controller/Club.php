<?php

namespace app\backend\controller;

use app\backend\model\Admin as AdminModel;
use app\service\JwtService;
use think\Response;

//俱乐部
class Club extends Base
{
    /**
     * 添加俱乐部
     * @param username 俱乐部名称
     * @return Response
     */
    public function add()
    {
        try {
            $username = input('username');
            
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), [], $e->getCode());
        }

        $this->success('ok', $res);
    }

    /**
     * 修改俱乐部信息
     * @return Response
     */
    public function update()
    {
        $this->success('ok', $this->userData);
    }
}
