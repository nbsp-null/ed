<?php

namespace app\admin\controller;

use app\admin\model\Classes;
use app\admin\model\Course;
use app\admin\model\Student;
use app\admin\model\Grade;
use app\admin\validate\StudentValidate;
use app\admin\model\Campus;
use Think\Upload;
use think\File;
use think\Db;

/**
 * 学员信息 Controller
 * peter
 * Class Student
 * @package app\admin\controller
 */
class StudentController extends CommonController {

    /**
     * 获得班级信息
     */
    public function getClasses() {

        $campusid = session("loginSession")['campusid'];
        $list = M('classes')->where(["campusid" => $campusid])->select();
        return json_encode($list);
    }

    /**
     * 获得课程信息
     */
    public function getCourse() {
        $campusid = session("loginSession")['campusid'];
        $list = Db::name('course')->select();
        return json_encode($list);
    }
    /**
     * 通过选的班级自动获取课程
     */
    public function getCourseByClass() {
        $courseid = Db::table('ew_classes')->where('id',$_POST['id'])->find()['classes_courseid'];
        $course = Db::table('ew_course')->where('id',$courseid)->find();
        return json_encode($course);
    }
    /**
     * 通过选的年级自动获取课程
     */
    public function getCourseByGrade() {
        $path = $this->getDataByCampusid();
        $course = Db::table('ew_course')->where('course_grade_id',$_POST['id'])->where($path)->select();
        return json_encode($course);
    }
    /**
     * 通过选的课程绑定班级
     */
    public function getClassByCourse() {
        $path = $this->getDataByCampusid();
        $classes = Db::table('ew_classes')->where($path)->where('classes_courseid',$_POST['id'])->select();//包含这些课程的班级
        if(empty($classes)){
            return json_encode($classes);die;
        }
        $res = array();
        foreach($classes as $value){//本班本课程有多少人了
            $studentNum = Db::table('ew_student')->where('student_classid',$value['id'])->where('student_courseid',$_POST['id'])->where('student_status',0)->where($path)->count();
           if($studentNum < $value['classes_planstudents']){
               $res[] = $value['id'];
           }
        }
        $result = Db::table('ew_classes')->where('id','in',$res)->select();
        return json_encode($result);
    }

    /**
     * 修改学生信息modal框
     * @return mixed
     * Author ghostsf
     * Blog www.ghostsf.com
     */
    public function updatemodal() {
        $campusid = session("loginSession")['campusid'];
        $id = input('id', '');
        $student = Student::get($id);
        $this->assign("student", $student);

        $classes = Classes::where(['campusid' => $campusid])->select();
        $courses = Course::where(['campusid' => $campusid])->select();
        $grades = Grade::where(['campusid' => $campusid])->select();
        $this->assign("classes", $classes);
        $this->assign("courses", $courses);
        $this->assign("grades", $grades);
        return $this->fetch("student/update");
    }

    /**
     * 插入学生数据
     * @return mixed
     */
    public function insert() {
        //获取登陆的校长的校区id
        $data = session("loginSession")['campusid'];

        $registrationModel = Db::name("Student");

        $validata = new StudentValidate();
        if (false) {//!$validata->check($_POST)
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else if(count(Db::name("Student")->select())<10000){
            $count = Db::table('ew_student')->where('campusid',$data)->where('student_gradeid', $_POST['student_gradeid'])->where('student_classid', $_POST['student_classid'])->count();
            $res = date('Y') . str_pad($_POST['student_gradeid'], 2, 0, STR_PAD_LEFT) . str_pad($_POST['student_classid'], 2, 0, STR_PAD_LEFT) . str_pad( ++$count, 2, 0, STR_PAD_LEFT);
            $_POST['student_studentid'] = (int) $res;
//            $_POST['student_createtime'] = date('Y-m-d H:i:s');
            $_POST['campusid'] = $data;
            $id = $registrationModel->insertGetId($_POST);
            $b = sprintf("%06d", $id);
            Db::name("user")->insert(["campusid" => $data, "username" => $b, "password" => $b, "typeid" => 1]);
            $returnData['status'] = 1;
            $returnData['msg'] = "<center><b style='color:blue;'>报名成功</b> <br/>账号密码为<b style='color:red;'>{$b}</b></center>";
            return json_encode($returnData);
		}else{
			$returnData['status'] = 0;
            $returnData['msg'] = "班级学员最多100名";
            return json_encode($returnData);
		}
    }


    /**
     * 更改
     * @return string
     * Author ghostsf
     * Blog www.ghostsf.com
     */
    public function update() {

        $registrationModel = M("Student");
        $validata = new StudentValidate();
        if (!$validata->check($_POST)) {
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else {
            $registrationModel->update($_POST);
            $returnData['status'] = 1;
            $returnData['msg'] = "修改成功";
            return json_encode($returnData);
        }
    }

    /**
     * 获得年级信息的json数据
     */
    public function getGradeJSON() {
		if($this->redis()){
            if($this->redis->EXISTS('DgetGradeJSON'))
               return $this->redis->get("DgetGradeJSON");

        }
        $campusid = session("loginSession")['campusid'];
        $list = Db::name("grade")->where(["campusid" => $campusid])->select();
		if($this->redis()) {
            $this->redis->set("DgetGradeJSON",json_encode($list));
        }
        return json_encode($list);
    }

    /**
     * 获得学生信息json数据
     */
    public function getStudents() {
        $start = "";
        if (empty($_POST['student_createtime'])) {
            $start = 0;
            unset($_POST['student_createtime']);
        } else {
            $start = $_POST['student_createtime'];
            unset($_POST['student_createtime']);
        };
        $rows = $_POST['rows'];
        $page = $_POST['page'];
        $path = $this->getDataByCampusid($_POST);
        //课程年级班级不能like
        $searchPath = $this->searchNotLike($path,$_POST,'student_gradeid','student_classid','student_courseid');
        //end
        if (isset($searchPath['campusid'])) {
            $searchPath['student.campusid'] = $searchPath["campusid"];
            unset($searchPath["campusid"]);
        }
        $students = Student::with("grade,course,classes")->where($searchPath)->where('student_createtime', '>', $start)->order("student.id desc")->limit($rows * ($page - 1), $rows)->select();
        $total = Student::with("grade,course,classes")->where($searchPath)->where('student_createtime', '>', $start)->count();
        $data['total'] = $total;
        $data['rows'] = $students;
        return json($data);
    }

    /**
     * 删除学生信息
     */
    public function deleteByIDs() {
        $ids = input('ids');
        $ids = rtrim($ids, ",");
        $map['id'] = array('in', $ids);
        $deleteinfo = Student::where($map)->delete();
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "删除成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "删除失败！"));
        }
    }

    /**
     * Author ghostsf
     * Blog www.ghostsf.com
     * 导出为excel
     */
    public function export() {
        $campusid = session('loginSession')['campusid'];
        $campus = Campus::get($campusid);
        $xlsName = $campus->campus_name . "校区学员信息汇总表";
        $xlsCell = array(
            array('student_name', '姓名'),
            array('student_studentid', '学号'),
            array('student_phone', '联系方式'),
            array('student_idcard', '身份证号'),
            array('student_school', '就读学校'),
            array('grade_name', '年级'),
            array('student_sex', '性别'),
            array('course_name', '选择课程'),
            array('student_cardnum', '支付宝账号'),
            array('student_createtime', '报名日期'),
            array('classes_name','班级'),
            array('student_status', '状态'),
            array('student_remark', '备注')
        );
        $join = [
            ['ew_grade grade', 'student.student_gradeid=grade.id'],
            ['ew_course course', 'student.student_courseid=course.id'],
            ['ew_classes classes','student.student_classid=classes.id'],
        ];
        $xlsData = Db::table('ew_student')->alias('student')->join($join)->order('student_studentid asc')->select();
        for ($i = 0; $i < count($xlsData); $i++) {
            if ($xlsData[$i]['student_status'] == 0) {
                $xlsData[$i]['student_status'] = "在读";
            } else {
                $xlsData[$i]['student_status'] = "毕业";
            }
            if ($xlsData[$i]['student_sex'] == 0) {
                $xlsData[$i]['student_sex'] = "男";
            } else {
                $xlsData[$i]['student_sex'] = "女";
            }
        }
        $this->exportExcel($xlsName, $xlsCell, $xlsData);
    }

    /**
     * 导入一定格式的数据
     */
    public function import() {
        $files = request()->file('Filedata');
        $file = $files->move('static/plugins/AjaxUpload/Upload');
        $fil = $file->getPathname();
        $array = $this->read($fil);
        
        $arr = array();
      
        $campusid = session('loginSession')['campusid'];
     
        for($i=2;$i<count($array)+1;$i++){
            foreach($array[$i] as $k=>$v){
                if($k==0){
                    $array[$i]['student_name'] = $v;
                    unset($array[$i][$k]);
                }
              
                if($k==1){
                    $array[$i]['student_phone'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==2){
                    $array[$i]['student_idcard'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==3){
                    $array[$i]['student_school'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==4){
                    $array[$i]['student_gradeid'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==5){
                    $array[$i]['student_sex'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==6){
                    $array[$i]['student_courseid'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==7){
                    $array[$i]['student_cardnum'] = $v;
                    unset($array[$i][$k]);
                }
                
                if($k==8){
                    $array[$i]['student_classid'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==9){
                    $array[$i]['student_status'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==10){
                    $array[$i]['student_remark'] = $v;
                    unset($array[$i][$k]);
                }
                $array[$i]['campusid'] = $campusid;
                $array[$i]['student_createtime'] = date("Y-m-d",time());
                $array[$i]['student_studentid'] = "";
            }
           $arr[$i-2]=$array[$i];
           
        }
      

        for($i=0;$i<count($arr);$i++){
                if( null != $a = Db::table("ew_grade")->field("id")->where(["grade_name"=>$arr[$i]["student_gradeid"],"campusid"=>$campusid])->select()){
                    $arr[$i]["student_gradeid"]=$a[0]['id'];
                }else{
                    $id = Db::table("ew_grade")->insertGetId(["campusid"=>$campusid,"grade_name"=>$arr[$i]["student_gradeid"]]);
                    $arr[$i]["student_gradeid"]=$id;
                }
          
                if(null != $a = Db::table("ew_course")->field("id")->where(["course_name"=>$arr[$i]["student_courseid"],"campusid"=>$campusid])->select()){
                    $arr[$i]["student_courseid"]=$a[0]['id'];
                }else{
                    $id = Db::table("ew_course")->insertGetId(["campusid"=>$campusid,"course_name"=>$arr[$i]["student_courseid"]]);
                    $arr[$i]["student_courseid"] = $id;
                }
                if(null != $a = Db::table("ew_classes")->field("id")->where(["classes_name"=>$arr[$i]["student_classid"],"campusid"=>$campusid])->select()){
                    $arr[$i]["student_classid"]=$a[0]['id'];
                }else{
                    $id = Db::table("ew_classes")->insertGetId(["campusid"=>$campusid,"classes_name"=>$arr[$i]["student_classid"]]);
                    $arr[$i]["student_classid"]=$id;
                }
                if($arr[$i]['student_sex']=='女'){
                    $arr[$i]['student_sex']=0;
                }else{
                    $arr[$i]['student_sex']=1;
                }
                if($arr[$i]['student_status']=='在读'){
                    $arr[$i]['student_status']=0;
                }else{
                    $arr[$i]['student_status']=1;
                }
                
         
        }

        for($k=0;$k<count($arr);$k++){
            $count = Db::table('ew_student')->where('student_gradeid', $arr[$k]['student_gradeid'])->where('student_classid', $arr[$k]['student_classid'])->count();
             if($arr[$k]["student_studentid"]==''){
                    $arr[$k]["student_studentid"]=date('Y') . str_pad($arr[$k]['student_gradeid'], 2, 0, STR_PAD_LEFT) . str_pad($arr[$k]['student_classid'], 2, 0, STR_PAD_LEFT) . str_pad( ++$count, 2, 0, STR_PAD_LEFT);;
                }
            $id = M("student")->insertGetId($arr[$k]);
            $b = sprintf("%06d", $id);
            M("user")->insert(["campusid" => $campusid, "username" => $b, "password" => $b, "typeid" => 1]);
        }
      
       
        
    }
  
} 