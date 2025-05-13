<?php

namespace app\app\controller;

use think\Request;
use app\backend\model\Banner as BannerModel;

class Banner extends Base
{
    /**
     * 获取banner列表
     * @param type 类型：1-首页 2-比赛页 3-活动页
     * @return array
     */
    public function list()
    {
        $type = input('type', 1);
        $list = BannerModel::where('status', 1)
            ->where('type', $type)
            ->order('sort', 'desc')
            ->select();
            
        $this->success('获取成功', $list);
    }

    /**
     * 添加banner
     * @return array
     */
    public function add(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('请求方式错误');
        }

        // 获取用户信息
        $userInfo = $this->getUserInfo();
        if (empty($userInfo)) {
            $this->error('用户未登录');
        }

        // 获取 JSON 数据
        $jsonData = json_decode(file_get_contents('php://input'), true);
        
        $data = [
            'title' => $jsonData['title'] ?? '',
            'image' => $jsonData['image'] ?? '',
            'url' => $jsonData['url'] ?? '',
            'sort' => $jsonData['sort'] ?? 0,
            'type' => $jsonData['type'] ?? 1,
            'status' => $jsonData['status'] ?? 1,
            'create_time' => time(),
            'update_time' => time()
        ];

        if (empty($data['title']) || empty($data['image'])) {
            $this->error('标题和图片不能为空');
        }

        BannerModel::create($data);
        $this->success('添加成功');
    }

    /**
     * 编辑banner
     * @return array
     */
    public function edit(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('请求方式错误');
        }

        // 获取用户信息
        $userInfo = $this->getUserInfo();
        if (empty($userInfo)) {
            $this->error('用户未登录');
        }

        // 获取 JSON 数据
        $jsonData = json_decode(file_get_contents('php://input'), true);
        
        $id = $jsonData['id'] ?? 0;
        if (empty($id)) {
            $this->error('ID不能为空');
        }

        $data = [
            'title' => $jsonData['title'] ?? '',
            'image' => $jsonData['image'] ?? '',
            'url' => $jsonData['url'] ?? '',
            'sort' => $jsonData['sort'] ?? 0,
            'type' => $jsonData['type'] ?? 1,
            'status' => $jsonData['status'] ?? 1,
            'update_time' => time()
        ];

        if (empty($data['title']) || empty($data['image'])) {
            $this->error('标题和图片不能为空');
        }

        BannerModel::where('id', $id)->update($data);
        $this->success('编辑成功');
    }

    /**
     * 删除banner
     * @return array
     */
    public function delete(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('请求方式错误');
        }

        // 获取用户信息
        $userInfo = $this->getUserInfo();
        if (empty($userInfo)) {
            $this->error('用户未登录');
        }

        // 获取 JSON 数据
        $jsonData = json_decode(file_get_contents('php://input'), true);
        
        $id = $jsonData['id'] ?? 0;
        if (empty($id)) {
            $this->error('ID不能为空');
        }

        BannerModel::where('id', $id)->update([
            'deleted_time' => time()
        ]);
        
        $this->success('删除成功');
    }
} 