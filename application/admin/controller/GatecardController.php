<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace app\admin\controller;
use app\admin\model\Schedule;
use app\admin\model\Classes;
use app\admin\model\Called;
use think\Db;
class GatecardController extends CommonController
{
    /**
     * 统计出勤率
     */
    public function Gatecard()
    {
        $rows = $_POST['rows'];
        $page = $_POST['page'];
        $path = $this->getDataByCampusid($_POST);
        $searchPath = $this->searchNotLike($path,$_POST,'schedule_courseid','schedule_classid');
	if(isset($searchPath['campusid'])){
            $searchPath['schedule.campusid'] = $searchPath["campusid"];
            unset($searchPath["campusid"]);
	}
        $searchPath['schedule_status'] = 1;
        $class = Schedule::with("classes,classroom,teacher,course")->field("count(schedule_classid) as countclass ,sum(schedule_actnum) as sumact,sum(schedule_sctnum) as sumsre ")->group("schedule_classid")->where($searchPath)->limit($rows * ($page - 1), $rows)->select();
        $total = Schedule::with("classes,classroom,teacher,course")->group("schedule_classid")->where($searchPath)->count();
        $data['total'] = $total;
        $data['rows'] = $class;
        return json_encode($data);
    }
}
