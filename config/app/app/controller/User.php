<?php

namespace app\app\controller;

use app\backend\model\User as UserModel;

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
     * @param string openid 用户openid
     * @param string nickname 昵称
     * @param string sex 性别 1：男、2：女
     * @param string phone 联系电话
     * @param string headimg 头像
     * @return array|false
    */
    public function updateInfo()
    {
        $openid = input('openid');
        $exit = UserModel::info($openid);
        $user['openid'] = input('openid');
        $user['nickname'] = input('nickname');
        $user['sex'] = input('sex');
        $user['phone'] = input('phone');
        $user['headimg'] = input('headimg');
        if ($exit) {
            UserModel::where('id', $exit['id'])->update($user);
        } else {
            (new UserModel())->save($user);
        }
        $this->success('ok', $user);
    }
}
