<?php
/**
 * Created by ghostsf
 * Number: n006833
 * Date: 2016/4/19
 */

namespace app\teacher\controller;


use app\admin\model\User;
use think\Controller;
use think\Db;

class LessionController extends controller{
    /*
     * 显示上课信息
     */
    public function schedule(){
        
        $id = session("login")["username"];
        $j = "";
        for($i=0;$i<strlen($id);$i++){
            if($id[$i]!="0"){
                $j = $i;
                break;
            }
        } 
        //获取老师id
        $teacherid = substr($id,$j);
        $join = [
          ["ew_course course","course.id = schedule.schedule_courseid"]  
        ];
        $datas = Db::table("ew_schedule")->field("schedule.id as scheduleid,schedule_starttime as starttime,course_name as name,schedule_status as status")->join($join)->alias("schedule")->where("schedule.schedule_teacherid = {$teacherid} && schedule_status != 2")->select();
        //dump($datas);
        for($i=0;$i<count($datas);$i++){
           $datas[$i]['starttime'] = substr($datas[$i]['starttime'],0,10);
        }
        $this->assign("datas",$datas);
        
        return $this->fetch("lession/index");
    }
    /**
     * 显示具体信息
     */
    public function detail(){
        $scheduleid = input("scheduleid");
         $join = [
          ["ew_course course","course.id = schedule.schedule_courseid"],
          ["ew_classes classes","classes.id = schedule.schedule_classid"],
          ["ew_grade grade","grade.id = schedule.schedule_gradeid"],
          ["ew_classroom classroom","classroom.id = schedule.schedule_classroomid"]   
        ];
        $datas = Db::table("ew_schedule")->join($join)->alias("schedule")->where("schedule.id = {$scheduleid}")->find();
        
        //dump($datas);
        if($datas["schedule_perweek"]=='1'){
            $datas["schedule_perweek"] = "星期一";
        }else if($datas["schedule_perweek"]=='2'){
            $datas["schedule_perweek"] = "星期二";
        }else if($datas["schedule_perweek"]=='3'){
            $datas["schedule_perweek"] = "星期三";
        }else if($datas["schedule_perweek"]=='4'){
            $datas["schedule_perweek"] = "星期四";
        }else if($datas["schedule_perweek"]=='5'){
            $datas["schedule_perweek"] = "星期五";
        }else if($datas["schedule_perweek"]=='6'){
            $datas["schedule_perweek"] = "星期六";
        }else if($datas["schedule_perweek"]=='7'){
            $datas["schedule_perweek"] = "星期天";
        }
        $num = strpos($datas['schedule_prenum'],"/");
        $datas['schedule_prenum'] = substr($datas["schedule_prenum"],0,$num);
        $this->assign("scheduleid",$scheduleid);
        $this->assign("datas",$datas);
        return $this->fetch("lession/detail");
    }
    /**
     * 开始上课
     */
    public function start()
    {
        $scheduleid = $_POST["scheduleid"];
        $content = $_POST["content"];
        $remark = $_POST["remark"];
        Db::table("ew_schedule")->alias("schedule")->where("id = {$scheduleid}")->update(["schedule_content"=>$content,"schedule_remark"=>$remark,"schedule_status"=>1]);
    }
    /**
     * 显示所有学生
     */
    public function student()
    {
       $classid =  input("classid");
       $scheduleid = input("scheduleid");
       $students = Db::table("ew_student")->alias("student")->where(["campusid"=>session("login")["campusid"],"student_classid"=>$classid,"student_status"=>0])->select();
       //dump($students);
       $this->assign("scheduleid",$scheduleid);
       $this->assign("students",$students);
       return $this->fetch("lession/student");
    }
    /**
     * 点名到课
     */
    public function arrive()
    {
        $studentid = input("studentid");
        $scheduleid = input("scheduleid");
        $campusid = session("login")["campusid"];
        $absent = input("absent");
        $evaluate = input("evaluate");
        $status = 1;
        $data = M("called")->where("called_scheduleid = {$scheduleid} && called_studentid={$studentid} && campusid={$campusid}")->select();
        if($data!=null){
            return json_encode(["status"=>1]);
        }
        M("called")->insert(["called_studentid"=>$studentid,"called_scheduleid"=>$scheduleid,"campusid"=>$campusid,"called_absent"=>$absent,"called_evaluate"=>$evaluate,"called_status"=>$status]);
       //获取实到人数
        $num = M("schedule")->where(["id"=>$scheduleid])->find()["schedule_actnum"];
        $num1 = $num+1;
        M("schedule")->where(["id"=>$scheduleid])->update(["schedule_actnum"=>$num1]);
    }
    /*
     * 点名旷课
     */
    public function absent()
    {
        $studentid = input("studentid");
        $scheduleid = input("scheduleid");
        $campusid = session("login")["campusid"];
        $absent = input("absent");
        $evaluate = input("evaluate");
        $status = 0;
        $data = M("called")->where("called_scheduleid = {$scheduleid} && called_studentid={$studentid} && campusid={$campusid}")->select();
        if($data!=null){
            return json_encode(["status"=>1]);
        }
         M("called")->insert(["called_studentid"=>$studentid,"called_scheduleid"=>$scheduleid,"campusid"=>$campusid,"called_absent"=>$absent,"called_evaluate"=>$evaluate,"called_status"=>$status]);
    }
    /*
     * 显示要评价的学生
     */
    public function allstudent()
    {
       $classid =  input("classid");
       $students = Db::table("ew_student")->alias("student")->where(["campusid"=>session("login")["campusid"],"student_classid"=>$classid,"student_status"=>0])->select();
       $this->assign("students",$students);
       return $this->fetch("lession/allstudent");
    }
    /**
     * 评价内容入库
     */
    public function feedback(){
       $evaluate = input("evaluate");
       $studentid = input("studentid");
       $data = M("student")->where("id = $studentid")->find();
       $gradeid = $data["student_gradeid"];
       $classid = $data["student_classid"];
       $campusid = session("login")["campusid"];
       $id = session("login")["username"];
        $j = "";
        for($i=0;$i<strlen($id);$i++){
            if($id[$i]!="0"){
                $j = $i;
                break;
            }
        }  
        //获取老师的id
        $teacherid= substr($id,$j);
        $time = date(("Y-m-d H:i:s"),time());
        $type=0;
        $info = M("feedback")->where("feedback_teacherid = {$teacherid} && feedback_studentid={$studentid} && feedback_type=0")->find();
        if($info!=null){
            return json_encode(["status"=>1]);
        }
        M("feedback")->insert(["feedback_classid"=>$classid,"feedback_gradeid"=>$gradeid,"feedback_teacherid"=>$teacherid,"feedback_studentid"=>$studentid,"campusid"=>$campusid,"feedback_content"=>$evaluate,"feedback_time"=>$time,"feedback_type"=>$type]);
    }
}

