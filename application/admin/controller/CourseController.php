<?php
/**
 * Created by ghostsf
 * Date: 2016/4/21
 */

namespace app\admin\controller;

use app\admin\model\Course;
use app\admin\model\Grade;
use app\admin\model\Subject;
use app\admin\model\Campus;
use think\Controller;
use app\admin\validate\CourseValidate;
use think\Db;


/**
 * 课程Controller
 * Class Course
 * @package app\admin\controller
 */
class CourseController extends CommonController
{
    /**
     *ajax获得课程信息
     */
    public function getCourseJSON()
    {	
		if($this->redis()){
            if($this->redis->EXISTS('DgetCourseJSON'))
               return $this->redis->get("DgetCourseJSON");

        }
		$campusid = session("loginSession")['campusid'];
        $list = Db::name('course')->where(["campusid"=>$campusid])->select();
		if($this->redis()) {
            $this->redis->set("DgetCourseJSON",json_encode($list));

        }
        return json_encode($list);
    }

    /**
     * 获得课程json数据
     */
    public function getCourses()
    {
        $rows = $_POST['rows'];
        $page = $_POST['page'];
        $path = $this->getDataByCampusid($_POST);
        $searchPath = $this->searchNotLike($path,$_POST,'course_grade_id','course_subject_id');
        if(isset($searchPath['campusid'])){
            $searchPath['course.campusid'] = $searchPath["campusid"];
            unset($searchPath["campusid"]);
        }
        $courses = Course::with("campus,grade,subject")->where($searchPath)->limit($rows * ($page - 1), $rows)->select();
        $total = Course::with("campus,grade,subject")->where($searchPath)->count();
        $data['total'] = $total;
        $data['rows'] = $courses;
        return json_encode($data);
    }
    /**
     * 显示添加页
     */
    public function addcourse()
    {
        $param = $this->getDataByCampusid();
        $grades = Grade::all($param);
        $subjects = Subject::all($param);

        $this->assign("grades",$grades);
        $this->assign("subjects",$subjects);
        return $this->fetch("course/add");
    }
    /**
     * 插入
     */
    public function insert()
    {

        $_POST['campusid'] = session('loginSession')['campusid'];

        $registrationModel = M("Course");
        $validata = new CourseValidate();
        if (!$validata->check($_POST)) {
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else {
            $_POST['course_total'] = $_POST['course_unitprice']*$_POST['course_periodnum'];
            $registrationModel->insert($_POST);
            $returnData['status'] = 1;
            $returnData['msg'] = "成功";
            return json_encode($returnData);
        }
    }
    /**
     * 显示修改页
     */
    public function updatecourse()
    {
        $param = $this->getDataByCampusid();
        $id = input('id', '');
        $course = Course::get($id);
        $this->assign("course", $course);
        $grades = Grade::all($param);
        $subjects = Subject::all($param);
        $campus = Campus::all();
        $this->assign("grades",$grades);
        $this->assign("subjects",$subjects);
        $this->assign("campus",$campus);
        return $this->fetch("course/update");
    }
    /**
     * 修改
     */
    public function update()
    {
        $registrationModel = M("Course");
        $validata = new CourseValidate();
        if (!$validata->check($_POST)) {
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else {
            $_POST['course_total'] = $_POST['course_unitprice']*$_POST['course_periodnum'];
            $registrationModel->update($_POST);
            $returnData['status'] = 1;
            $returnData['msg'] = "修改成功";
            return json_encode($returnData);
        }
    }
    /**
     * 删除
     */
    public function deleteByIDs()
    {
        $ids = input('ids');
        $ids = rtrim($ids, ",");
        $map['id'] = array('in', $ids);
        $deleteinfo = Db::name('course')->where($map)->delete();
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "删除成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "删除失败！"));
        }
    }

}