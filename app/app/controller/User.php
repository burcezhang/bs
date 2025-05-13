<?php

namespace app\app\controller;

use app\backend\model\User as UserModel;
use think\Request;

class User extends Base
{
    /**
     * 获取用户信息
     * @param string openid 用户openid
     * @return array|false
    */
    public function info()
    {
        $openid = input('openid');
        $user = UserModel::info($openid);
        // todo
        if($user){
            $user['level_name'] = '123';
        }
        $this->success('ok', $user);
    }

    /**
     * 修改用户信息
     * @param string headimg 头像
     * @param string nickname 昵称
     * @param string phone 手机号
     * @return array|false
    */
    public function updateInfo(Request $request)
    {
        // 获取token
        $token = $request->header('Authorization');
        if (empty($token)) {
            $this->error('请先登录');
        }

        // 获取POST数据
        $jsonData = json_decode(file_get_contents('php://input'), true);
        
        // 根据token查询用户
        $user = UserModel::where('token', $token)->find();
        if (!$user) {
            $this->error('用户不存在');
        }

        // 更新用户信息
        $updateData = [
            'headimg' => $jsonData['headimg'] ?? $user['headimg'],
            'nickname' => $jsonData['nickname'] ?? $user['nickname'],
            'phone' => $jsonData['phone'] ?? $user['phone'],
            'update_time' => time()
        ];

        // 更新用户信息
        UserModel::where('id', $user['id'])->update($updateData);

        $this->success('更新成功', $updateData);
    }

     /**
     * 用户积分榜
     * @param string club_id 俱乐部id
     * @return array|false
    */
    public function scoreList()
    {
        $club_id = input('club_id', 1);
        $page = input('page', 1);
        $limit = input('limit', 20);
        $res = (new UserModel)->scoreList($club_id, $page, $limit);
        $this->success('ok', $res);
    }
}
