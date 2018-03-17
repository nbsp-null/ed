<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/6/16
 */

namespace app\admin\controller;
use app\admin\model\Classes;
use app\admin\model\Classroom;
use app\admin\model\Course;
use app\admin\model\Grade;
use app\admin\model\Teacher;
use app\admin\model\Campus;
use app\admin\model\Schedule;
use app\admin\validate\ArrangeValidate;
use think\Db;
/**
 * 班级
 * Class ClassesController
 * Author mww
 * @package app\admin\controller
 */
class ArrangeController extends CommonController
{
    /**
     * 获得数据
     * Author mww
     */
    public function getDatas()
    {
        $rows = $_POST['rows'];
        $page = $_POST['page'];
        $status = '';
        //过滤未上课和已上课的条件
        if(isset($_POST['schedule_status'])){
            $status .= $_POST['schedule_status'].',';
            unset($_POST['schedule_status']);
        };
        if(isset($_POST['schedule_status1'])){
            $status .= $_POST['schedule_status1'].',';
            unset($_POST['schedule_status1']);
        };
        $status = trim($status,',');
        //===================过滤结束==================
        //过滤日期范围
        $start = "";
        $end = "";
        if(empty($_POST['schedule_starttime'])){
            $start = 0;
            unset($_POST['schedule_starttime']);
        }else{
            $start = $_POST['schedule_starttime'];
            unset($_POST['schedule_starttime']);
        };
        if(empty($_POST['schedule_endtime'])){
            $end = '2100-12-31';
            unset($_POST['schedule_endtime']);
        }else{
            $end = $_POST['schedule_endtime'];
            unset($_POST['schedule_endtime']);
        };
        //过滤结束==============================
        $path = $this->getDataByCampusid($_POST);
        $searchPath = $this->searchNotLike($path,$_POST,'schedule_teacherid','schedule_classid','schedule_classroomid');
        if(isset($searchPath['campusid'])){
            $searchPath['schedule.campusid'] = $searchPath["campusid"];
            unset($searchPath["campusid"]);
        }
        $class = Schedule::with("classroom,teacher,classes")->where($searchPath)->where('schedule_status','not in',$status)->where('schedule_starttime','between',"{$start},{$end}")->limit($rows * ($page - 1), $rows)->select();
        $total = Schedule::with("classroom,teacher,classes")->where($searchPath)->where('schedule_status','not in',$status)->where('schedule_starttime','between',"{$start},{$end}")->count();
        $data['total'] = $total;
        $data['rows'] = $class;
        return json_encode($data);
    }

    public function addarrange()
    {
        $param = $this->getDataByCampusid();
        $courses = Course::all();
        $classes = Classes::all($param);
        $classroom = Classroom::all($param);
        $teachers = Teacher::all($param);
        $grades = Grade::all($param);
        $this->assign('courses',$courses);
        $this->assign('classes',$classes);
        $this->assign('classroom',$classroom);
        $this->assign('teachers',$teachers);
        $this->assign('grades',$grades);
        $this->assign('user',session('loginSession')['typeid']);
        return $this->fetch("arrange/add");
    }
    public function insert()
    {
        if(isNotNull(session('loginSession')['campusid'])){
            $_POST['campusid'] = session('loginSession')['campusid'];
        }

        $validata = new ArrangeValidate();
        if (!$validata->check($_POST)) {
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else {
            $datas = $this->arrangeDateSet($_POST);
            if(isset($datas['msg'])){
                return json_encode($datas);
            }
            //将课时数和教室和教师补齐到班级信息里
            $class['classes_plantimes'] = floor(($_POST['schedule_classlength']*count($datas))/60).':'.($_POST['schedule_classlength']*count($datas))%60;
            $class['classes_headteacher'] = $_POST['schedule_teacherid'];
            $class['classes_classroomid'] = $_POST['schedule_classroomid'];
            Db::table('ew_classes')->where('id',$_POST['schedule_classid'])->update($class);
            //==========补齐结束======
            foreach($datas as $vv){
                Db::table('ew_schedule')->insert($vv);
            }
            $returnData['status'] = 1;
            $returnData['msg'] = '插入成功';
            return json_encode($returnData);

        }
    }
    //调课
    public function updateOne()
    {
        $param = $this->getDataByCampusid();
        $id = input('id', '');
        $schedule = Schedule::get($id);
        $this->assign("schedule", $schedule);
        $classes = Classes::get($schedule['schedule_classid']);
        $classrooms = Classroom::get($schedule['schedule_classroomid']);
        $teachers = Teacher::all($param);
        $grades = Grade::get($schedule['schedule_gradeid']);
        $courses = Course::get($schedule['schedule_courseid']);
        $this->assign('grades',$grades);
        $this->assign('courses',$courses);
        $this->assign('classes',$classes);
        $this->assign('classroom',$classrooms);
        $this->assign('teachers',$teachers);
        
        return $this->fetch("arrange/update");
    }
    //批量修改
    public function updateAll()
    {
        $param = $this->getDataByCampusid();
        $id = input('id', '');
        $scheduleO = Schedule::get($id);
        $paramAll = $this->getNeedData($scheduleO);
        $schedule = Db::table('ew_schedule')->where($paramAll)->where($param)->order("schedule_starttime")->limit(0,1)->find();

        unset($schedule['schedule_perweek']);
        $test = Db::table('ew_schedule')->where($paramAll)->where($param)->group("schedule_perweek")->select();
        foreach($test as $value){
            $schedule['schedule_perweek'][] = $value['schedule_perweek'];
        };
        $this->assign("schedule", $schedule);
        $classes = Classes::all($param);
        $classrooms = Classroom::all($param);
        $teachers = Teacher::all($param);
        $grades = Grade::all($param);
        $courses = Course::all($param);
        $this->assign('grades',$grades);
        $this->assign('courses',$courses);
        $this->assign('classes',$classes);
        $this->assign('classroom',$classrooms);
        $this->assign('teachers',$teachers);

        return $this->fetch("arrange/updateAll");
    }
    //修改调课
    public function update()
    {
        $registrationModel = M("Schedule");
       // $_POST['schedule_perweek'] = date('w',strtotime($_POST['schedule_starttime']));
        $result = $registrationModel->update($_POST);
        if ($result == true) {
            Db::table('ew_schedule')->where('id',$_POST['id'])->update(['schedule_update'=>1]);
            $returnData['status'] = 1;
            $returnData['msg'] = "修改成功";
            return json_encode($returnData);
        } else {

            $returnData['status'] = 0;
            $returnData['msg'] = "您并未做任何改动";
            return json_encode($returnData);
        }
    }
    //修改排课
    public function updateDone()
    {
        if(isNotNull(session('loginSession')['campusid'])){
            $_POST['campusid'] = session('loginSession')['campusid'];
        }
        $validata = new ArrangeValidate();
        if (!$validata->check($_POST)) {
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else {
            $id = $_POST['id'];
            $schedule = Schedule::get($id);
            $param = $this->getNeedData($schedule);
            $param['schedule_status'] = 0;
            if(isNotNull(session('loginSession')['campusid'])){
                $param['campusid'] = session('loginSession')['campusid'];
            }
            Db::startTrans(); // 事务操作防止修改不成功的时能不删除数据
            $deletes = Db::table('ew_schedule')->where($param)->delete();

                unset($_POST['id']);
                $datas = $this->arrangeDateSet($_POST);
                if(isset($datas['msg'])){
                    Db::rollback();
                    return json_encode($datas);die;
                };

                //将课时数和教室和教师补齐到班级信息里
                $min = ($_POST['schedule_classlength']*count($datas))%60;
                if($min<10){
                    $min = '0'.$min;
                }
                $class['classes_plantimes'] = floor(($_POST['schedule_classlength']*count($datas))/60).':'.$min;
                $class['classes_headteacher'] = $_POST['schedule_teacherid'];
                $class['classes_classroomid'] = $_POST['schedule_classroomid'];
                Db::table('ew_classes')->where('id',$_POST['schedule_classid'])->update($class);
                //==========补齐结束======
                foreach($datas as $vv){
                    Db::table('ew_schedule')->insert($vv);
                }
            Db::commit();
                $returnData['status'] = 1;
                $returnData['msg'] = '修改成功';
                return json_encode($returnData);
           


        }
        

        
       
    }
    public function getIdStatus()
    {
        $param = $this->getDataByCampusid();
        $schedule = Schedule::all($param);
        $result = array();
        foreach($schedule as $val){
            $result[$val['id']] = $val['schedule_status'];
        }
        
        return json_encode($result);
    }
    //留待备用（删除多条）====================================
    public function deleteByIDs()
    {
        $ids = input('ids');
        $ids = rtrim($ids, ",");
        $map['id'] = array('in', $ids);
        $deleteinfo = Schedule::where($map)->delete();
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "删除成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "删除失败！"));
        }
    }
    //=============================================================
    //取消上课
    public function cancelLesson()
    {
        //2代表取消上课
        $schedule = Schedule::where('id',$_POST['id'])->update(['schedule_status'=>2]);
        if($schedule){
            return json_encode(array("status" => 1, "msg" => '已取消上课'));
        }else{
            return json_encode(array("status" => 0, "msg" => "找不到数据！"));
        }
    }
    //撤销上课
    public function backLesson()
    {
        //0代表未上课
        $schedule = Schedule::where('id',$_POST['id'])->update(['schedule_status'=>0]);
        if($schedule){
            return json_encode(array("status" => 1, "msg" => '已撤消'));
        }else{
            return json_encode(array("status" => 0, "msg" => "找不到数据！"));
        }
    }
    //点名上课
    public function deleteLesson()
    {
        //0代表未上课
        $schedule = Schedule::where('id',$_POST['id'])->delete();
        if($schedule){
            return json_encode(array("status" => 1, "msg" => '已删除'));
        }else{
            return json_encode(array("status" => 0, "msg" => "找不到数据！"));
        }
    }
    //查看教师空闲信息
    public function teacherFree()
    {
        return $this->fetch('arrange/teacherFree');
    }
    //选择要查看的教师==========暂时保留==============
    public function choiseTeacher()
    {
        $param = $this->getDataByCampusid();
        $teachers = Teacher::all($param);
        $this->assign('teachers',$teachers);
        return $this->fetch('arrange/choiseTeach');
    }
    //=============================================
    //展示教师信息
    public function displayTeacher()
    {
        $start = "";
        $end = "";
        if(empty($_POST['schedule_starttime'])){
            return json_encode(array("status" => 0, "msg" => "请选择开始日期"));
        }else{
            $start = $_POST['schedule_starttime'];
        };
        if(empty($_POST['schedule_endtime'])){
            $end = '2100-12-31';
        }else{
            $end = $_POST['schedule_endtime'];
        };
        if($end <= $start){
            return json_encode(array("status" => 0, "msg" => "请选择正确的日期范围"));
        }
        $teachers = $this->getFreeTeacher($start,$end);
      if(empty($teachers)){
            return json_encode(array("status" => 0, "msg" => "此范围内没有空闲老师"));
        }
        return json_encode(array("status" => 1, "msg" => $teachers));
    }
    //查看教室空闲信息
    public function classroomFree()
    {
        return $this->fetch('arrange/classroomFree');
    }
     //展示教室信息
    public function displayClassroom()
    {
        $start = "";
        $end = "";
        if(empty($_POST['schedule_starttime'])){
            return json_encode(array("status" => 0, "msg" => "请选择开始日期"));
        }else{
            $start = $_POST['schedule_starttime'];
        };
        if(empty($_POST['schedule_endtime'])){
            $end = '2100-12-31';
        }else{
            $end = $_POST['schedule_endtime'];
        };
        if($end <= $start){
            return json_encode(array("status" => 0, "msg" => "请选择正确的日期范围"));
        }
        $classrooms = $this->getFreeClassroom($start,$end);
        if(empty($classrooms)){
            return json_encode(array("status" => 0, "msg" => "此范围内没有空闲教室"));
        }
        return json_encode(array("status" => 1, "msg" => $classrooms));
    }
    //根据日期查看教室教师班级信息
    public function getFreeDatas()
    {
        $start = $_POST['start'];
        $end = $_POST['end'];
        $result = array();
        $result['freeTeachers'] = $this->getFreeTeacher($start,$end);
        $result['freeClassrooms'] = $this->getFreeClassroom($start,$end);
        $result['freeClasses'] = $this->getFreeClass($start,$end);
        return json_encode(array("status" => 1, "msg" => $result));
    }
    //根据日期查看教室教师班级信息
    public function updateGetFreeDatas()
    {
        $id = $_POST['id'];
        $schedule = Schedule::get($id);
        $param = $this->getNeedData($schedule);
        $param['schedule_status'] = 0;
        if(isNotNull(session('loginSession')['campusid'])){
            $param['campusid'] = session('loginSession')['campusid'];
        }
//        Db::startTrans(); // 事务操作防止修改不成功的时能不删除数据
        Db::table('ew_schedule')->where($param)->delete();
        $start = $_POST['start'];
        $end = $_POST['end'];
        $result = array();
        $result['freeTeachers'] = $this->getFreeTeacher($start,$end);
        $result['freeClassrooms'] = $this->getFreeClassroom($start,$end);
        $result['freeClasses'] = $this->getFreeClass($start,$end);
//        Db::rollback();
        return json_encode(array("status" => 1, "msg" => $result));
    }
    /**
     * 实时更新排课里的已招人数
     */
    public function updateArrange()
    {
        $arranges = Db::table('ew_schedule')->field('schedule_classid,schedule_endtime,schedule_prenum,schedule_courseid,schedule_gradeid')->where('schedule_status',0)->where('campusid',session("loginSession")['campusid'])->group('schedule_endtime,schedule_classid,schedule_courseid,schedule_gradeid')->select();
		print_r($arranges);
        if(!empty($arranges)){
			echo 'a';
            $a = 0;
            foreach($arranges as $key=>$arrange){
            $path['student_classid'] = $arrange['schedule_classid'];
            $path['student_courseid'] = $arrange['schedule_courseid'];
            $path['student_gradeid'] = $arrange['schedule_gradeid'];
           $num = Db::table('ew_student')->where($path)->where('student_status',0)->where('campusid',session("loginSession")['campusid'])->count();
            $prenum = explode('/',$arrange['schedule_prenum']);
            if($num != $prenum[0]){
                $a++;
                $newPrenum = $num.'/'.$prenum[1];
                unset($arrange[$key]['schedule_prenum']);
                Db::table('ew_schedule')->where($arranges[$key])->where('schedule_status',0)->where('campusid',session("loginSession")['campusid'])->update(['schedule_prenum'=>$newPrenum,'schedule_sctnum'=>$num]);
            }
        }
            return $a;
        }else{
            return 0;
        }
        
    }
}