<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/6/16
 */

namespace app\admin\controller;
use app\admin\model\Classes;
use app\admin\model\Classroom;
use app\admin\model\Teacher;
use app\admin\model\Campus;
use app\admin\model\Course;
use app\admin\validate\ClassValidate;
use think\Db;
/**
 * 班级
 * Class ClassesController
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\admin\controller
 */
class ClassesController extends CommonController
{
    /**
     *ajax获得班级JSON
     */
    public function getClassesJSON()
    {	
		if($this->redis()){
            if($this->redis->EXISTS('DgetClassesJSON'))
               return $this->redis->get("DgetClassesJSON");

        }
        $path = $this->getDataByCampusid();
        $list = Db::name('classes')->where($path)->select();
        //通过classid和status限制合格的才显示（没有courseid）
        if(empty($list)){
            return json_encode($list);
        }else {
            $res = array();
            foreach ($list as $value) {//本班本课程有多少人了
                $studentNum = Db::table('ew_student')->where('student_classid', $value['id'])->where('student_status', 0)->where($path)->count();
                if ($studentNum < $value['classes_planstudents']) {
                    $res[] = $value['id'];
                }
            }
            
            $result = Db::table('ew_classes')->where('id', 'in', $res)->select();
            //===================
		if($this->redis()) {
            $this->redis->set("DgetClassesJSON",json_encode($result));

        }
            return json_encode($result);
        }
    }
    /**
     * 获得数据
     * Author mww
     */
    public function getDatas()
    {
        $rows = $_POST['rows'];
        $page = $_POST['page'];
        $path = $this->getDataByCampusid($_POST);
        $searchPath = $this->searchNotLike($path,$_POST,'classes_headteacher','classes_courseid','classes_classroomid');
        if(isset($searchPath['campusid'])){
            $searchPath['classes.campusid'] = $searchPath["campusid"];
            unset($searchPath["campusid"]);
        }
        $class = Classes::with("campus,classroom,teacher,course")->where($searchPath)->limit($rows * ($page - 1), $rows)->select();
        $total = Classes::with("campus,classroom,teacher,course")->where($searchPath)->count();
        foreach($class as $key=>$value){
            if(empty($class[$key]['Teacher'])){
                $class[$key]['status'] = '<font color="red">未排课</font>';
            }else{
                $class[$key]['status'] = '已排课';
            }
        }
        $data['total'] = $total;
        $data['rows'] = $class;
        return json_encode($data);
    }

    public function addclass()
    {
        $param = $this->getDataByCampusid();
        $classrooms = Classroom::all($param);
        $teachers = Teacher::all($param);
        $courses = Course::all($param);
        $this->assign('courses',$courses);
        $this->assign('classrooms',$classrooms);
        $this->assign('teachers',$teachers);
        return $this->fetch("classes/add");
    }
    public function insert()
    {
        if(isNotNull(session('loginSession')['campusid'])){
            $_POST['campusid'] = session('loginSession')['campusid'];
        }
      
        $registrationModel = Db::name("Classes");
        $validata = new ClassValidate();
        if (!$validata->check($_POST)) {
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else {
            $registrationModel->insert($_POST);
            $returnData['status'] = 1;
            $returnData['msg'] = "成功";
            return json_encode($returnData);
        }
    }
    public function updateclass()
    {
        $param = $this->getDataByCampusid();
        $id = input('id', '');
        $class = Classes::get($id);
        $this->assign("class", $class);
        $campus = Campus::all();
        $classrooms = Classroom::all($param);
        $teachers = Teacher::all($param);
        $courses = Course::all($param);
        $this->assign('courses',$courses);
        $this->assign('campus',$campus);
        $this->assign('classrooms',$classrooms);
        $this->assign('teachers',$teachers);
        return $this->fetch("classes/update");
    }
    public function update()
    {
        $registrationModel = Db::name("Classes");
        $validata = new ClassValidate();
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
    public function deleteByIDs()
    {
        $ids = input('ids');
        $ids = rtrim($ids, ",");
        $map['id'] = array('in', $ids);
        $deleteinfo = Db::name('classes')->where($map)->delete();
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "删除成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "删除失败！"));
        }
    }
}