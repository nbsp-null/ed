<?php

namespace app\admin\controller;

use app\admin\model\Subject;
use app\admin\model\Grade;
use app\admin\model\Teacher;
use app\admin\validate\TeacherValidate;
use app\admin\model\Campus;
use think\Db;

/**
 * 教师Controller
 * Class TeacherController
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\admin\controller
 */
class TeacherController extends CommonController {

    /**
     * ajax获得年级信息
     */
    public function getGrade() {
		if($this->redis()){
            if($this->redis->EXISTS('DgetGrade'))
               return $this->redis->get("DgetGrade");

        }
        $campusid = session("loginSession")['campusid'];
        $list = $this->arr_index(Grade::where(["campusid" => $campusid])->select());
		if($this->redis()) {
            $this->redis->set("DgetGrade",json_encode($list));
        }
        $list = json_encode($list);

        return $list;
    }

    /**
     * 插入老师数据
     * @return mixed
     */
    public function insert() {
        //获取登陆的校长的校区id
        $data = session("loginSession")['campusid'];
        $registrationModel =Db::name("Teacher");
        $validata = new TeacherValidate();
        if($_POST['teacher_befulldate']=="")$_POST["teacher_befulldate"]=time();
        if (false) {//!$validata->check($_POST)
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else if( count(Db::name("Teacher")->select())<5){
            $_POST['campusid'] = $data;
            $id = $registrationModel->insertGetId($_POST);
            $b = sprintf("%06d", $id);
            Db::name("user")->insert(["campusid" => $data, "username" => $b, "password" => $b, "typeid" => 2]);
            $returnData['status'] = 1;
            $returnData['msg'] = "<center><b style='color:blue;'>报名成功</b> <br/>账号密码为<b style='color:red;'>{$b}</b></center>";
            return json_encode($returnData);
		}else{
			$returnData['status'] = 0;
            $returnData['msg'] = "最多只能添加5位老师";
			return json_encode($returnData);
		}
    }

    /**
     * 更新教师数据
     * @return mixed
     */
    public function update() {

        $registrationModel = Db::name("Teacher");
        $validata = new TeacherValidate();
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
     * 获得教师信息json数据
     */
    public function getTeachers() {
        $rows = $_POST['rows'];
        $page = $_POST['page'];
        $path = $this->getDataByCampusid($_POST);
        //课程年级班级不能like
        if (isset($searchPath['teacher_subject_id'])) {
            unset($searchPath["teacher_subject_id"]);
            $searchPath['teacher_subject_id'] = $_POST['teacher_subject_id'];

        }
        if (isset($searchPath['teacher_grade_id'])) {
            unset($searchPath["teacher_grade_id"]);
            $searchPath['teacher_grade_id'] = $_POST['teacher_grade_id'];

        }
        $searchPath = $this->searchNotLike($path,$_POST,'teacher_subject_id','teacher_grade_id');
        //end
        if (isset($searchPath['campusid'])) {
            $searchPath['teacher.campusid'] = $searchPath["campusid"];
            unset($searchPath["campusid"]);
        }
        $students = Teacher::with("grade,subject")->where($searchPath)->order("teacher.id desc")->limit($rows * ($page - 1), $rows)->select();
        $total = Teacher::with("grade,subject")->where($searchPath)->count();
        $data['total'] = $total;
        $data['rows'] = $students;
        return json_encode($data);
    }

    /**
     * 根据ID获得教师信息json数据
     */
    public function getTeacherById() {
        $teacher_id = input('teacher_id', '');
        $where = array();
        if (isNotNull($teacher_id)) {
            $where['id'] = $teacher_id;
        }
        $teachers = Teacher::where($where)->limit(1)->find();

        return json_encode($teachers);
    }

    /**
     * 	修改页面的显示
     */
    public function updatemodal() {
        $campusid = session("loginSession")['campusid'];
        $id = input('id');
        //获取要修改的老师信息
        $teacher = Teacher::get($id);
        $grades = Grade::where(["campusid" => $campusid])->select();
        $subjects = Subject::where(["campusid" => $campusid])->select();

        $this->assign("teacher", $teacher);
        $this->assign("grades", $grades);
        $this->assign("subjects", $subjects);
        return $this->fetch('teacher/update');
    }

    /**
     * 删除老师信息
     */
    public function deleteByIDs() {
        $ids = input('ids');
        $ids = rtrim($ids, ",");
        $map['id'] = array('in', $ids);
        $deleteinfo = Teacher::where($map)->delete();
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "删除成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "删除失败！"));
        }
    }

    public function arr_index($arrs) {
        $arr_index = array();
        foreach ($arrs as $arr) {
            $arr_index[$arr['id']] = $arr;
        }
        return $arr_index;
    }
    /*
     * 导出老师excel表格
     */
    public function export()
    {
        $campusid = session('loginSession')['campusid'];
        $campus = Campus::get($campusid);
        $xlsName = $campus->campus_name . "校区老师信息汇总表";
        $xlsCell = array(
            array("teacher_name","姓名"),
            array("teacher_gender","性别"),
            array("teacher_idcard","身份证"),
            array("teacher_bankaccount","银行卡号"),
            array("teacher_jobtype","在职类型"),
            array("grade_name","年级"),
            array("subject_name","科目"),
            array("teacher_telphone","电话"),
            array("teacher_email","邮箱"),
            array("teacher_qq","qq"),
            array("teacher_joindate","入职日期"),
            array("teacher_befulldate","转正日期"),
            array("teacher_status","是否在职"),
            array("teacher_remark","备注")
            );
        $join = [
            ["ew_grade grade","teacher.teacher_grade_id = grade.id"],
            ["ew_subject subject","teacher.teacher_subject_id = subject.id" ]
        ];
        $xlsData = Db::table('ew_teacher')->alias('teacher')->join($join)->order('teacher_joindate asc')->select();
         for ($i = 0; $i < count($xlsData); $i++) {
            if ($xlsData[$i]['teacher_gender'] == 0) {
                $xlsData[$i]['teacher_gender'] = "男";
            } else {
                $xlsData[$i]['teacher_gender'] = "女";
            }
            if ($xlsData[$i]['teacher_jobtype'] == 0) {
                $xlsData[$i]['teacher_jobtype'] = "兼职";
            } else {
                $xlsData[$i]['teacher_jobtype'] = "全职";
            }
            if($xlsData[$i]['teacher_status']==0){
                $xlsData[$i]['teacher_status'] = "离职";
            }else{
                $xlsData[$i]['teacher_status'] = "在职";
            }
        }
        $this->exportExcel($xlsName, $xlsCell, $xlsData);
    }
    /*
     * 老师信息导出
     */
    public function import()
    {
        $files = request()->file('Filedata');
        $file = $files->move('static/plugins/AjaxUpload/Upload');
        $fil = $file->getPathname();
        $array = $this->read($fil);
        //dump($array);
         $arr = array();
       
       // $campusid = $_POST["campusid"];
        $campusid = session('loginSession')['campusid'];
        for($i=2;$i<count($array)+1;$i++){
            foreach($array[$i] as $k=>$v){
                if($k==0){
                    $array[$i]['teacher_name'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==1){
                    $array[$i]['teacher_gender'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==2){
                    $array[$i]['teacher_idcard'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==3){
                    $array[$i]['teacher_bankaccount'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==4){
                    $array[$i]['teacher_jobtype'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==5){
                    $array[$i]['teacher_grade_id'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==6){
                    $array[$i]['teacher_subject_id'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==7){
                    $array[$i]['teacher_telphone'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==8){
                    $array[$i]['teacher_email'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==9){
                    $array[$i]['teacher_qq'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==10){
                    $array[$i]['teacher_joindate'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==11){
                    $array[$i]['teacher_befulldate'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==12){
                    $array[$i]['teacher_status'] = $v;
                    unset($array[$i][$k]);
                }
                if($k==13){
                    $array[$i]['teacher_remark'] = $v;
                    unset($array[$i][$k]);
                }
                $array[$i]['campusid'] = $campusid;
            }
           $arr[$i-2]=$array[$i];
           
        }
       
         for($i=0;$i<count($arr);$i++){
                
                if( null != $a = Db::table("ew_grade")->field("id")->where(["grade_name"=>$arr[$i]["teacher_grade_id"],"campusid"=>$campusid])->select()){
                    $arr[$i]["teacher_grade_id"]=$a[0]['id'];
                }else{
                    $id = Db::table("ew_grade")->insertGetId(["campusid"=>$campusid,"grade_name"=>$arr[$i]["teacher_grade_id"]]);
                    $arr[$i]["teacher_grade_id"]=$id;
                }
                if( null != $a = Db::table("ew_subject")->field("id")->where(["subject_name"=>$arr[$i]["teacher_subject_id"],"campusid"=>$campusid])->select()){
                    $arr[$i]["teacher_subject_id"]=$a[0]['id'];
                }else{
                    $id = Db::table("ew_subject")->insertGetId(["campusid"=>$campusid,"subject_name"=>$arr[$i]["teacher_subject_id"]]);
                    $arr[$i]["teacher_subject_id"]=$id;
                }
                if($arr[$i]['teacher_jobtype']=='全职'){
                    $arr[$i]['teacher_jobtype']=1;
                }else{
                    $arr[$i]['teacher_jobtype']=0;
                }
                if($arr[$i]['teacher_gender']=='男'){
                    $arr[$i]['teacher_gender']=0;
                }else{
                    $arr[$i]['teacher_gender']=1;
                }
                if($arr[$i]['teacher_status']=='离职'){
                    $arr[$i]['teacher_status']=0;
                }else{
                    $arr[$i]['teacher_status']=1;
                }
        }
       
        for($k=0;$k<count($arr);$k++){
           $id =  M("teacher")->insertGetId($arr[$k]);
           $b = sprintf("%06d", $id);
           M("user")->insert(["campusid" => $campusid, "username" => $b, "password" => $b, "typeid" => 2]);
        }
        
        
    }
}
