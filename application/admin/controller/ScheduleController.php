<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/6/16
 */

namespace app\admin\controller;
use app\admin\model\Schedule;
use app\admin\model\Classes;
use app\admin\model\Called;
use think\Db;
/**
 * 排课 Main
 * Class ScheduleController
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\admin\controller
 */
class ScheduleController extends CommonController
{
	/**
	 *	获取课表信息
	 */
	public function getDatas()
	{

	$rows = $_POST['rows'];
        $page = $_POST['page'];
        $path = $this->getDataByCampusid($_POST);
		$searchPath = $this->searchNotLike($path,$_POST,'schedule_classid','schedule_classroomid');
	if(isset($searchPath['campusid'])){
            $searchPath['schedule.campusid'] = $searchPath["campusid"];
            unset($searchPath["campusid"]);
	}
		
        $class = Schedule::with("classes,classroom,teacher")->where($searchPath)->limit($rows * ($page - 1), $rows)->select();
        $total = Schedule::with("classes,classroom,teacher")->where($searchPath)->count();
        $data['total'] = $total;
        $data['rows'] = $class;
        return json_encode($data);
	}
	/**
	 *  添加课程备注
	 **/
	public function mark()
	{
		$schedule_content = input("schedule_content");
		$schedule_remark = input("schedule_remark");
		$id = input("id");
		M("schedule")->where(["id"=>$id])->update(["schedule_content"=>$schedule_content,"schedule_remark"=>$schedule_remark]);
		return json_encode(["status"=>1,"msg"=>"成功"]);
	}
	
	/**
	 * 驱动查看到课情况页面
	 */	
	 
	public function getSchedule()
	{
		$id = input('id');
		$sid = input('sid');
		$this->assign("id",$id);
		$this->assign("sid",$sid);
		$schedules = Schedule::get($id);
		$schedule_starttime = $schedules->schedule_starttime; 
		$schedule_starttime = substr($schedule_starttime,0,10);
		$classid = $schedules->schedule_classid;
		$classes = Classes::get($classid);
		$this->assign("schedule_starttime",$schedule_starttime);
		$this->assign("schedules",$schedules);
		$this->assign("classes",$classes);
		return $this->fetch("schedule/update");
	}
	


	
	/**
	 * 查询这节课旷课的人
	 */
	public function getlatestudent()
	{
		$rows = $_POST['rows'];
                $page = $_POST['page'];
		$id = $_POST['id'];
		$students =  Called::with("student")->where(["called_scheduleid"=>$id])->limit($rows * ($page - 1), $rows)->select();
                $total = Called::with("student")->where(["called_scheduleid"=>$id])->count();
                $data['rows'] = $students;
                $data['total'] = $total;
		return json_encode($data);
	}
	

}


























