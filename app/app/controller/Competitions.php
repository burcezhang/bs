<?php

namespace app\app\controller;

use app\backend\model\CompetitionRegistrations;
use app\backend\model\Competitions as CompetitionsModel;

//比赛活动
class Competitions extends Base
{
    /**
     * 添加比赛、活动活动
     * @return array|false
    */
    public function add()
    {
        $data = [
            'name' => input('name'),
            'club_id' => input('club_id'),
            'start_time' => input('start_time'),
            'location' => input('location'),
            'location_place' => input('location_place'),
            'max_players' => input('max_players'),
            'description' => input('description'),
            'organizer' => input('organizer'),
            'type' => input('type'),
            'openid' => input('openid'),
            'banner' => input('banner'),
        ];
        (new CompetitionsModel())->save($data);
        $this->success('ok', $data);
    }

    /**
     * 比赛、活动列表
     * @param type 1-比赛 2-活动
     * @return array|false
    */
    public function list()
    {
        $type = input('type');
        $page = input('page', 1);
        $limit = input('limit', 20);
        $res = (new CompetitionsModel())->listType($type, $page, $limit);
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
    public function registration()
    {
        $id = input('id');
        $openid = input('openid');
        $competition = (new CompetitionsModel())->detail($id);
        if (!$competition) {
            $this->error('比赛不存在，请核对比赛详情', [], 902);
        }
        $isRegistration = (new CompetitionRegistrations())->isRegistration($id, $openid);
        if ($isRegistration) {
            $this->error('您已报名，请勿重复报名', [], 901);
        } else {
            $reg = [
                'competition_id' => $id,
                'openid' => $openid,
                'type' => $competition['type'],
            ];
            (new CompetitionRegistrations())->save($reg);
            $this->success('ok');
        }
    }

    /**
     * 我参加的比赛、活动
     * @param openid 用户openid
     * @param type 1-比赛 2-活动
     * @return array|false
    */
    public function myList()
    {
        $type = input('type');
        $openid = input('openid');
        $page = input('page', 1);
        $limit = input('limit', 20);
        $userList = (new CompetitionRegistrations())->userList($openid, $type, $page, $limit);
        $competitionIds = array_column($userList['list'], 'competition_id');
        $list = (new CompetitionsModel())->listByIds($competitionIds);
        $res = [
            'list' => $list,
            'count' => $userList['count'],
        ];
        $this->success('ok', $res);
    }

}
