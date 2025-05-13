<?php

namespace app\app\controller;

use app\backend\model\CompetitionRegistrations;
use app\backend\model\Competitions as CompetitionsModel;
use think\Request;
use think\facade\Config;
use think\facade\Cache;

//比赛活动
class Competitions extends Base
{
    /**
     * 添加比赛、活动活动
     * @return array|false
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
            'name' => $jsonData['name'] ?? '',
            'club_id' => $jsonData['club_id'] ?? '',
            'start_time' => $jsonData['start_time'] ?? '',
            'location' => $jsonData['location'] ?? '',
            'location_place' => $jsonData['location_place'] ?? '',
            'max_players' => $jsonData['max_players'] ?? '',
            'description' => $jsonData['description'] ?? '',
            'organizer' => $jsonData['organizer'] ?? '',
            'type' => $jsonData['type'] ?? '',
            'openid' => $userInfo['openid'],
            'banner' => $jsonData['banner'] ?? '',
        ];
        
        if (empty($data['name']) || empty($data['club_id'])) {
            $this->error('必填参数不能为空');
        }
        
        (new CompetitionsModel())->save($data);
        $this->success('ok', $data);
    }

    /**
     * 比赛、活动列表
     * @param type 1-比赛 2-活动（可选）
     * @param date 日期
     * @return array|false
    */
    public function list()
    {
        $type = input('type', '');
        $date = input('date', '');
        $page = input('page', 1);
        $limit = input('limit', 20);
        $res = (new CompetitionsModel())->listType($type, $date, $page, $limit);
        foreach ($res['list'] as &$val) {
            $val['countdown'] = '30天10小时';
        }
        $res['page'] = $page;
        $this->success('ok', $res);
    }

    /**
     * 比赛、活动详情
     * @param type 1-比赛 2-活动
     * @return array|false
    */
    public function detail()
    {
        $id = input('id');
        $res = (new CompetitionsModel())->detail($id);
        // todo
        $res['club_name'] = '测试俱乐部';
        $this->success('ok', $res);
    }

    /**
     * 报名比赛、活动
     * @param id 比赛、活动id
     * @param openid 用户openid
     * @return array|false
    */
    public function registration(Request $request)
    {
        // 尝试多种方式获取id
        $id = $request->param('id');
        if (empty($id)) {
            $id = $request->post('id');
        }
        if (empty($id)) {
            $jsonData = json_decode(file_get_contents('php://input'), true);
            $id = $jsonData['id'] ?? '';
        }

        if (empty($id)) {
            $this->error('比赛ID不能为空');
        }

        // 获取用户信息
        $userInfo = $this->getUserInfo();
        if (empty($userInfo)) {
            $this->error('用户未登录');
        }

        $competition = (new CompetitionsModel())->detail($id);
        if (!$competition) {
            $this->error('比赛不存在，请核对比赛详情', [], 902);
        }

        $isRegistration = (new CompetitionRegistrations())->isRegistration($id, $userInfo['openid']);
        if ($isRegistration) {
            $this->error('您已报名，请勿重复报名', [], 901);
        } else {
            $reg = [
                'competition_id' => $id,
                'openid' => $userInfo['openid'],
                'type' => $competition['type'],
            ];
            (new CompetitionRegistrations())->save($reg);
            $this->success('ok');
        }
    }


    /**
     * 报名人员列表
     * @param id 活动、比赛id
     * @return array|false
    */
    public function regList()
    {
        $id = input('id');
        $type = input('type', 1);
        $page = input('page', 1);
        $limit = input('limit', 20);
        $competition = (new CompetitionRegistrations())->regList($id, $type, $page, $limit);
        $this->success('ok', $competition);
    }

     /**
     * 取消报名人员
     * @param id 列表id
     * @return array|false
    */
    public function regCancel()
    {
        $id = input('id');
        $competition = (new CompetitionRegistrations())->regCancel($id);
        $this->success('ok');
    }

    /**
     * 我参加的比赛、活动
     * @param type 1-比赛 2-活动
     * @return array|false
    */
    public function myList()
    {
        // 获取用户信息
        $userInfo = $this->getUserInfo();
        if (empty($userInfo)) {
            $this->error('用户未登录');
        }

        $type = input('type');
        $page = input('page', 1);
        $limit = input('limit', 20);
        $userList = (new CompetitionRegistrations())->userList($userInfo['openid'], $type, $page, $limit);
        $competitionIds = array_column($userList['list'], 'competition_id');
        $list = (new CompetitionsModel())->listByIds($competitionIds);
        $res = [
            'list' => $list,
            'count' => $userList['count'],
        ];
        $this->success('ok', $res);
    }

}
