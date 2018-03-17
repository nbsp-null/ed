<?php
namespace app\admin\controller;

use app\admin\model\Menus;
use app\admin\model\Campus;
use app\admin\model\Student;
use app\admin\model\Classes;
use app\admin\model\Teacher;
use think\Controller;
use think\Db;


/**
 * 后台主页
 * Class IndexController
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\admin\controller
 */
class IndexController extends CommonController
{
    /**
     *
     * @return mixed
     */
    public function index()
    {
        
        
        $userSession = session('loginSession');
        
        if (!$userSession) {
            $this->setGlobalSettings(["GlobalTitle", "LoginPageTitle", "DialogClassSpeed", "DialogClassAnimation", "LoginPageSTitle"]);
            return $this->fetch('index');
        } else {
           
            
            $this->setGlobalSettings(["DialogClassSpeed", "DialogClassAnimation", "GlobalTitle", "BackGroundTitle"]);
            $this->setPersonalSettings(["openAnimation"]);

            $menus = Menus::all();
            $menunames = array();
            $menuicons = array();
            foreach ($menus as $menu) {
                $menunames[$menu['menu_value']] = $menu['menu_name'];
                $menuicons[$menu['menu_value']] = $menu['menu_iconclass'];
            }
            $this->assign("menunames", $menunames);
            $this->assign("menuicons", $menuicons);
            $this->assign("user", $userSession);
            
            $campus = Campus::all();
            $this->assign("campus", $campus);
            
            if($userSession['typeid']==0&&empty($_POST['campusId'])){
                //计算各个校区昨天的收入
                $yesterday = date('Y-m-d',strtotime('-1 day')).' 00:00:00';
                $today = date('Y-m-d',strtotime('-1 day')).' 23:59:59';
                foreach($campus as $key=>$campu){
                    $student = Db::table('ew_student')->field('count(id) as num,student_courseid')->where('campusid',$campu['id'])->where('student_createtime','between',"$yesterday,$today")->group('student_courseid')->select();
                    $totalPrice = 0;
                    foreach($student as $stu){
                        $course = Db::table('ew_course')->field('course_unitprice,course_periodnum')->where('id',$stu['student_courseid'])->find();
                        $price = $course['course_unitprice']*$course['course_periodnum']*$stu['num'];
                        $totalPrice += $price;
                    }
                    $campus[$key]['income'] = $totalPrice;
                    if(mb_strlen($campus[$key]['campus_address'],'utf8')>8){
                        $campus[$key]['campus_address'] = mb_substr($campus[$key]['campus_address'],0,8,'utf8').'...';
                    };
                    if(mb_strlen($campus[$key]['campus_remark'],'utf8')>8){
                        $campus[$key]['campus_remark'] = mb_substr($campus[$key]['campus_remark'],0,8,'utf8').'...';
                    };
                }
                //==============计算结束=============
                return $this->fetch("campus");
            }
             if(!empty($_POST['campusId'])){
                 $userSession['campusid']=$_POST['campusId'];
            }
            
            $campus_name = Campus::where(array('id'=>$userSession['campusid']))->field('campus_name')->select();
            $student_total = Student::where(array('campusid'=>$userSession['campusid']))->count('id');
            $teacher_total = Teacher::where(array('campusid'=>$userSession['campusid']))->count('id');
            $classes_total = Classes::where(array('campusid'=>$userSession['campusid']))->count('id');

            $this->assign('student_total',$student_total);
            $this->assign('teacher_total',$teacher_total);
            $this->assign('classes_total',$classes_total);
            
            $this->assign('campus_name',$campus_name);
            
            return $this->fetch('admin');
        }
    }
    


}
