<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\admin\controller;

use app\admin\model\Subject;
use app\admin\model\Grade;
use think\Db;

class DashboardController extends CommonController {

    /**
     * 
     * 获取对应校区学生的总数
     */
    public function getStudentNum() {
		if($this->redis()){
            if($this->redis->EXISTS('DgetStudentNum'))
			return $this->redis->get("DgetStudentNum");
        }
        $campusid = session("loginSession")['campusid'];

        date_default_timezone_set("PRC");
        //计算当前年份
        $ThisTime = date("Y", time());

        $data = M("student")->where(" campusid = $campusid &&  student_createtime >= '{$ThisTime}-01-01' && student_createtime <= '{$ThisTime}-12-31'")->field("month(student_createtime) as name,count(student_createtime) as value")->group("month(student_createtime)")->select();
        $array = array();
        for ($i = 0; $i <= 11; $i++) {
            $array[$i]["name"] = $i + 1;
            $array[$i]["value"] = 0;
        }
        $arr = array();
        foreach ($array as $a) {
            foreach ($data as $b) {
                if ($a['name'] == $b['name']) {
                    $a['value'] = $b['value'];
                } 
            }
            array_push($arr, $a);
        }
        $ar = $this->sum($arr);
		if($this->redis()) {
            $this->redis->set("DgetStudentNum", json_encode($ar));
            $this->redis->expire('DgetStudentNum', 7200);
        }
        return json_encode($ar);
    }

    /*
     * 用于累加的方法
     */

    public function sum($arr)
    {
        $c= "";
        $ar = array();
        for ($i = 0; $i <= 11; $i++) {
            $ar[$i]["name"] = $i + 1;
            $ar[$i]["value"] = 0;
        }
        foreach($arr as $k=>$v){
            $c += $v['value'];
            $ar[$k]['value'] = $c;
        }
        return $ar;
    }

    /*
     * 获取老师数据总数
     */
    public function getTeacherNum()
    {
        $campusid = session("loginSession")['campusid'];
		if($this->redis()){
            if($this->redis->EXISTS('DgetTeacherNum'))
                return $this->redis->get("DgetTeacherNum");
        }
        date_default_timezone_set("PRC");
        //计算当前年份
        $ThisTime = date("Y", time());

        $data = M("teacher")->where(" campusid = $campusid &&  teacher_joindate >= '{$ThisTime}-01-01' && teacher_joindate <= '{$ThisTime}-12-31'")->field("month(teacher_joindate) as name,count(teacher_joindate) as value")->group("month(teacher_joindate)")->select();
        $array = array();

        for ($i = 0; $i <= 11; $i++) {
            $array[$i]["name"] = $i + 1;
            $array[$i]["value"] = 0;
        }
        $arr = array();
        foreach ($array as $a) {
            foreach ($data as $b) {
                if ($a['name'] == $b['name']) {
                    $a['value'] = $b['value'];
                } 
            }
            array_push($arr, $a);
        }
        $ar = $this->sum($arr);
		if($this->redis()) {
            $this->redis->set("DgetTeacherNum", json_encode($ar));
            $this->redis->expire('DgetTeacherNum', 7200);
        }
        return json_encode($ar);
    }        

    /*
     * 按学校给学生分组
     */

    public function getSchool() {
		if($this->redis()){
            if($this->redis->EXISTS('DgetSchool'))
                return $this->redis->get("DgetSchool");
        }
        $campusid = session("loginSession")["campusid"];
        $data = M("student")->where("campusid = $campusid ")->field('student_school as name,count(student_school) as value')->group("student_school")->select();
		if($this->redis()) {
            $this->redis->set("DgetSchool", json_encode($data));
            $this->redis->expire('DgetSchool', 7200);
        }
        return json_encode($data);
    }

                                                                           


   //课程统计学生数
    public function getStudentByCourse()
    {	
		if($this->redis()){
            if($this->redis->EXISTS('DgetStudentByCourse'))
                return $this->redis->get("DgetStudentByCourse");
        }
        $campusid = session("loginSession")['campusid'];
        $datas = Db::table('ew_student')
            ->join('ew_course','ew_student.student_courseid=ew_course.id')
            ->where('ew_student.campusid',$campusid)
            ->field('ew_course.course_name as name,count(ew_student.student_courseid) as value')
            ->group('ew_student.student_courseid')
            ->select();
		if($this->redis()) {
            $this->redis->set("DgetStudentByCourse", json_encode($datas));
            $this->redis->expire('DgetStudentByCourse', 7200);
        }
        return json_encode($datas);
    }
    
    /**
     * 
     * 分页填充数据
     */
    public function notice()
    {
         if (input("page") == NULL) {
            $page = 1;
        } else {
            $page = input("page");
        }
        if($page < 1){
            $page =1;
        }
        $rows = 5;
        $campusid = session("loginSession")['campusid'];
        $count = M("notice")->where(["campusid" => $campusid, "type" => '1', "status" => '1'])->count();
        //总页数
        $pagecount = ceil($count/$rows);
        if($page>$pagecount){
            $page = $pagecount;
        }
        
        $data = M("notice")->where(["campusid" => $campusid, "type" => '1', "status" => '1'])->order("time desc")->limit($rows * ($page - 1), $rows)->select();
        
        $this->assign("count", $count);

       
        //dump($data);
        $this->assign("page", $page);
        $this->assign("datas", $data);
        return $this->fetch('notice/watch');
    }
}

