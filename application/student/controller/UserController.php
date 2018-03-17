<?php
/**
 * Created by ghostsf
 * Date: 2016/4/19
 */
namespace app\student\controller;

use app\admin\model\User;
use think\Controller;
use think\Db;


class UserController extends Controller
{
    /**
     * @return int  登录验证
     */

    public function loginValidate()
    {
        $username = input('UserName');
        $password = input('PassWord');
        if (isNotNull($username) && isNotNull($password)) {
            $student = User::get(array("username" => $username, "password" => $password,'typeid'=>1));
            if ($student) {
                session('loginStudent', $student);
                return json_encode(["status"=>1,"info"=>"登陆成功"]);
            } else {
                session("loginStudent", null);
                return json_encode(["status"=>0,"info"=>"账户或者密码不正确"]);
            }
        } else {
            return 0;
        }
    }
    /**
     * 退出
     */
    public function loginOut()
    {
        session("loginStudent", null);
        return 1;
    }
/**
 * 显示个人信息
 */
    public function selfManage()
    {
        $user = session('loginStudent');

        $id = ltrim($user['username'],'0');
        $classid = Db::table('ew_student')->where('id',$id)->find()['student_classid'];
        $teacherid = Db::table('ew_classes')->where('id',$classid)->find()['classes_headteacher'];
        $teacher = Db::table('ew_teacher')->where('id',$teacherid)->find()['teacher_name'];
        $value = Db::table('ew_student')->join('ew_classes','ew_student.student_classid=ew_classes.id')
                                ->join('ew_grade','ew_student.student_gradeid=ew_grade.id')
                                ->join('ew_course','ew_student.student_courseid=ew_course.id')
                                ->where('ew_student.id',$id)
                                ->find();
        $value['teacher_name'] = $teacher;
        $value['campus_name'] = Db::table('ew_campus')->where('id',$user['campusid'])->find()['campus_name'];
        $this->assign('studentManage',$value);
        return $this->fetch('self/manage');

    }
/**
 * 查看课表
 */
    public function getSchedule()
    {
        $user = session('loginStudent');
        $id = ltrim($user['username'],'0');
        $student = Db::table('ew_student')->where('id',$id)->find();
        //
        $sch = Db::table('ew_called')->where('called_studentid',$id)->where('campusid',$user['campusid'])->find();
        if(empty($sch)){
            $schedule = Db::table('ew_schedule')->alias('s')
                ->join('ew_teacher t','s.schedule_teacherid=t.id')
                ->join('ew_course c','s.schedule_courseid=c.id')
                ->join('ew_classroom r','s.schedule_classroomid=r.id')
                ->where('s.campusid',$user['campusid'])
                ->where('s.schedule_classid',$student['student_classid'])
                ->where('s.schedule_courseid',$student['student_courseid'])
                ->where('s.schedule_status',0)
                ->field('s.id,schedule_starttime,schedule_status')
                ->select();
        }else{
            $schid = $sch['called_scheduleid'];
            $data = Db::table('ew_schedule')->where('id',$schid)->find();
            $schedule = Db::table('ew_schedule')->alias('s')
                ->join('ew_teacher t','s.schedule_teacherid=t.id')
                ->join('ew_course c','s.schedule_courseid=c.id')
                ->join('ew_classroom r','s.schedule_classroomid=r.id')
                ->where('s.campusid',$user['campusid'])
                ->where('s.schedule_classid',$student['student_classid'])
                ->where('s.schedule_courseid',$student['student_courseid'])
                ->where('s.schedule_endtime',$data['schedule_endtime'])
                ->field('s.id,schedule_starttime,schedule_status')
                ->select();
        }


        for($i=0;$i<count($schedule);$i++){
            $schedule[$i]['schedule_starttime'] = substr($schedule[$i]['schedule_starttime'],0,10);
        }
        $this->assign('schedule',$schedule);
        return $this->fetch('self/schedule');
    }
    /**
     * 查看公告
     */
    public function getNotice()
    {
        $user = session('loginStudent');
        $notices = Db::table('ew_notice')->where('campusid',$user['campusid'])->where('type',2)->select();
        $this->assign('notices',$notices);
        return $this->fetch('self/notice');
    }
    /**
     * 查看评价
     */
    public function getFeedback()
    {
        $user = session('loginStudent');
        $id = ltrim($user['username'],'0');
        $backs = Db::table('ew_feedback')->join('ew_teacher','ew_feedback.feedback_teacherid=ew_teacher.id')
            ->where('ew_feedback.campusid',$user['campusid'])
            ->where('ew_feedback.feedback_studentid',$id)
            ->where('ew_feedback.feedback_type',0)
            ->field('feedback_content,feedback_time,teacher_name')
            ->select();
        $this->assign('backs',$backs);
        return $this->fetch('self/feedback');
    }
    /**
     * 查看课程详细信息
     */
    public function lessionManage()
    {
        $id = input('id');
        $schedule = Db::table('ew_schedule')->alias('s')
            ->join('ew_teacher t','s.schedule_teacherid=t.id')
            ->join('ew_course c','s.schedule_courseid=c.id')
            ->join('ew_classroom r','s.schedule_classroomid=r.id')
            ->where('s.id',$id)
            ->find();
        $this->assign('lession',$schedule);
        return $this->fetch('self/lessionManage');
    }
    /**
     * 评价老师（每一节课的评价）
     */
    public function rateTeacher()
    {
        $user = session('loginStudent');
        $id = ltrim($user['username'],'0');
        $student = Db::table('ew_student')->where('id',$id)->find();
        $data['feedback_teacherid'] = $_POST['teacherid'];
        $data['feedback_content'] = $_POST['content'];
        $data['feedback_studentid'] = $id;
        $data['feedback_classid'] = $student['student_classid'];
        $data['feedback_gradeid'] = $student['student_gradeid'];
        $data['feedback_type'] = 1;
        $data['feedback_time'] = date('Y-m-d H:i:s');
        $data['campusid'] = $user['campusid'];
        $feed = Db::table('ew_feedback')->insert($data);
        if($feed){
            return 1;
        }else{
            return 0;
        }
    }
    /**
     * 修改个人信息的数据
     */
    public function updateData()
    {
        $data['title'] = input('title','');
        $data['content'] = input('content','');
        $data['name'] = input('name','');
        $this->assign('data',$data);
        return $this->fetch('self/update');
    }
    /**
     * 修改密码
     */
    public function modify()
    {
        $user = session('loginStudent');

    }

    public function modifyData()
    {
        $data['title'] ='密码修改';
        $data['name']="username";
        $data['content']='';
        $this->assign('data',$data);
        return $this->fetch('self/update');
    }
    /**
     * 修改个人信息
     */

    public function update()
    {
        $user = session('loginStudent');
        if(array_key_exists("username",$_POST)){
            Db::name('user')->where('typeid',1)->where("username",$user['username'])->update(['password'=>$_POST['username']]);

            return 1;
        }
        $id = ltrim($user['username'],'0');
        Db::table('ew_student')->where('id',$id)->update($_POST);
        return 1;
    }
}