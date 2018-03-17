<?php

/**
 * Created by ghostsf
 * Number: n006833
 * Date: 2016/4/20
 */

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Grade;
use app\admin\model\Campus;
use app\admin\model\Classroom;
use app\admin\model\Teacher;
use app\admin\model\Classes;
use app\admin\model\Course;
use app\admin\model\Subject;
use app\admin\model\Schedule;
use app\admin\model\Student;
use think\Db;

/**
 * ajax 异步加载页面
 * Class Ajax
 * @package app\admin\controller
 */
class AjaxController extends CommonController {
   

    /**
     * 仪表盘
     */
    public function dashboard() {
		$campusid_l=session('loginSession')['campusid'];

        $count =Db::name("notice")->where(["campusid" => $campusid_l, "type" => '1', "status" => '1'])->count();

        $data = Db::name("notice")->where(["campusid" => $campusid_l, "type" => '1', "status" => '1'])->order("time desc")->limit(0,4)->select();
        $this->assign("datas", $data);
        $this->assign("count", $count);

        //生日提醒
        $day = date("md", time());
        $birthday = array();
        $name = array();
        $res = Db::name("teacher")->field("teacher_idcard,teacher_name")->select();
        while (list($key, $value) = each($res)) {
            array_push($name, $value['teacher_name']);
            array_push($birthday, $value['teacher_idcard']);


        }
        $bir = array();
        foreach ($birthday as $key => $v) {
            if (substr($v, 10, 4) == $day) {
                array_push($bir, $name[$key]);
            }
        }
   
        $this->assign("num",count($bir));
        $this->assign("birthday",$bir);

        //获取挡前月份1号
        $BeginDate=date('Y-m-01', strtotime(date("Y-m-d")));
        $shangDate=date('Y-m-01', strtotime('-1 month'));;
        //dump($data);
   
        $student_total = Db::name("student")->where(array('campusid'=>$campusid_l))->count('id');
        $teacher_total = Db::name("teacher")->where(array('campusid'=>$campusid_l))->count('id');
        $classes_total = Db::name("classes")->where(array('campusid'=>$campusid_l))->count('id');

        $shangNum=Db::name("student")->where("student_createtime",">","{$BeginDate}")->count("id");
		if($shangNum!=null){
        $num=Db::query("select count(id)as num from ew_student WHERE  student_createtime  >'{$shangDate}' and student_createtime < '{$BeginDate}'");
		if($num[0]['num']==0||$num[0]['num']='null')
		{
			$res="0%";
		}
		else{
		$res=(round($shangNum/$num[0]['num'],2)*100)."%";
		}
		$this->assign('v',$res);
		}else{
		$this->assign('v',"0%");	
		}
		
        $this->assign('student_total',$student_total);
        $this->assign('teacher_total',$teacher_total);
        $this->assign('classes_total',$classes_total);

        return $this->fetch('dashboard');
    }
    
    /**
     * 菜单管理
     */
    public function menu_manage() {
        return $this->fetch('menu/manage');
    }

    /**
     * 课程管理
     */
    public function course_manage() {
        $this->setPersonalSettings(["coursegridsize"]);
        $param = $this->getDataByCampusid();
        $grade = Grade::all($param);
        $subject = Subject::all($param);
        $this->assign('grades', $grade);
        $this->assign('subjects', $subject);
        return $this->fetch('course/manage');
    }

    /**
     * 学生报名
     * @return mixed
     */
    public function student_registration() {
        return $this->fetch('student/registration');
    }

    /**
     * 学生信息管理
     * @return mixed
     */
    public function student_manage() {
        $campusid = session("loginSession")['campusid'];
        $Course = Db::name('course')->where(["campusid" => $campusid])->select();
        $Classes =Db::name('classes')->where(["campusid" => $campusid])->select();
        $Grade =  Db::name('grade')->where(["campusid" => $campusid])->select();
        $this->assign("campusid", $campusid);
        $this->assign('Course', $Course);
        $this->assign('Grade', $Grade);
        $this->assign('Classes', $Classes);
        $this->setPersonalSettings(["studentgridsize"]);
        return $this->fetch('student/manage');
    }

    /**
     * 科目管理
     * @return mixed
     */
    public function subject_manage() {
        return $this->fetch('subject/manage');
    }

    /**
     * 年级管理
     * @return mixed
     */
    public function grade_manage() {
        return $this->fetch('grade/manage');
    }

    /**
     * 教室管理
     * @return mixed
     */
    public function classroom_manage() {
        return $this->fetch('classroom/manage');
    }

    /**
     * 教师注册
     * @return mixed
     */
    public function teacher_registration() {
        return $this->fetch('teacher/registration');
    }

    /**
     * 教师信息管理
     * @return mixed
     */
    public function teacher_manage() {
        $campusid = session("loginSession")['campusid'];
        $this->setPersonalSettings(["teachergridsize"]);
        $this->assign("campusid", $campusid);
        return $this->fetch('teacher/manage');
    }

    /**
     * 师生消息
     */
    public function feedback_manage() {
        $campusid = session("loginSession")['campusid'];
        $students = Db::name('student')->where(['campusid' => $campusid])->select();
        $teachers = Db::name('teacher')->where(['campusid' => $campusid])->select();
        $this->assign("students", $students);
        $this->assign("teachers", $teachers);
        return $this->fetch('feedback/manage');
    }

    /**
     * 公告信息管理
     * @return mixed
     */
    public function notice_manage() {
        $admin = Db::table('ew_admin')->where('campusid',session('loginSession')['campusid'])->whereOr('typeid',0)->select();
        $this->assign('admins',$admin);
        return $this->fetch('notice/manage');
    }

    /**
     * 校区信息管理
     * @return mixed
     */
    public function campus_manage() {
        $this->assign('user', session('loginSession')['typeid']);
        return $this->fetch('campus/manage');
    }

    /**
     * 校长信息管理
     * @return mixed
     */
    public function admin_manage() {
        $campus = Campus::all();
        $this->assign('campus', $campus);
        $this->assign('user', session('loginSession')['typeid']);
        return $this->fetch('admin/manage');
    }

    /**
     * 个性化设置
     * @return mixed
     */
    public function personal_settings() {
        // 当前用户若没有个性设置 则显示系统默认设置 
        //userid为0的为默认设置
        $sets = Db::table('ew_personalconfig')->where("userid", session('loginSession')['id'])->select();
        if (count($sets) < 1) {
            $sets = Db::table('ew_config')->where("id", '>', 129)->select();
        }
        $this->assign('sets', $sets);
        return $this->fetch('personal/setting');
    }

    /**
     * 系统设置
     * @return mixed
     */
    public function global_config() {
        $configs = Db::table('ew_config')->where(array("type" => 1))->select();
        $this->assign('configs', $configs);
        return $this->fetch('config/manage');
    }

    /**
     * 教务设置--班级管理
     * @return mixed
     */
    public function class_manage() {
        $param = $this->getDataByCampusid();
        $classrooms = Classroom::all($param);
        $teachers = Teacher::all($param);
        $courses = Course::all($param);
        $this->assign('courses', $courses);
        $this->assign('classrooms', $classrooms);
        $this->assign('teachers', $teachers);
        return $this->fetch('classes/manage');
    }

    /**
     * 教务设置--排课管理
     * @return mixed
     */
    public function course_arrange() {
        $param = $this->getDataByCampusid();
        $classes = Classes::all($param);
        $classroom = Classroom::all($param);
        $teachers = Teacher::all($param);
        $grades = Grade::all($param);
        $courses = Course::all($param);
        $this->assign('classes', $classes);
        $this->assign('classroom', $classroom);
        $this->assign('teachers', $teachers);
        $this->assign('grades', $grades);
        $this->assign('courses', $courses);
        return $this->fetch('arrange/manage');
    }

    /**
     * 教务设置--课表和点名
     * @return mixed
     */
    public function schedule_rollcall() {
        $campusid = session("loginSession")['campusid'];
        $classes = Db::name('classes')->where(["campusid" => $campusid])->select();
        $classrooms = Db::name('classroom')->where(["campusid" => $campusid])->select();
        $this->assign("classes", $classes);
        $this->assign("classrooms", $classrooms);
        return $this->fetch('schedule/manage');
    }

    /**
     * 出勤率����
     */
    public function gate_card() {
        $campusid = session("loginSession")['campusid'];
        $classes = Db::name('classes')->where(["campusid" => $campusid])->select();
        $courses = Db::name('course')->where(["campusid" => $campusid])->select();
        $this->assign("classes", $classes);
        $this->assign("courses", $courses);
        return $this->fetch("card/manage");
    }

    /**
     * �课消明细�����
     */
    public function school_lession() {
        $campusid = session("loginSession")['campusid'];
        $course = Db::name('course')->where(["campusid" => $campusid])->select();

        $this->assign("courses", $course);

        return $this->fetch("schoollession/manage");
    }



    
}
