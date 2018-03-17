<?php
/**
 * Created by ghostsf
 * Date: 2016/6/7
 */

namespace app\admin\controller;

use app\admin\model\Classroom;
use app\admin\model\Campus;
use app\admin\validate\ClassroomValidate;
use think\Db;

/**
 * 教室管理
 * Class ClassroomController
 * @package app\admin\controller
 */
class ClassroomController extends CommonController
{
    /**
     *ajax获得教室JSON
     */
    public function getJSON()
    {
        $list = Classroom::all();
        return json_encode($list);
    }
    

    /**
     * 获得数据
     */
    public function getDatas()
    {
        $rows = $_POST['rows'];
        $page = $_POST['page'];
        $_POST = $this->getDataByCampusid($_POST);
        $class = Classroom::with("campus")->where($_POST)->limit($rows * ($page - 1), $rows)->select();
        $total = Classroom::where($_POST)->count();
        $data['total'] = $total;
        $data['rows'] = $class;
        return json_encode($data);
    }
    /**
     * 显示添加页
     */
    public function addroom()
    {
        return $this->fetch("classroom/addroom");
    }
    /**
     * 插入数据
     */
    public function insert()
    {
            $_POST['campusid'] = session('loginSession')['campusid'];
        $registrationModel = M("Classroom");
        $validata = new ClassroomValidate();
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
    /**
     * 显示修改页
     */
    public function updateroom()
    {
        $id = input('id', '');
        $classroom = Classroom::get($id);
        $this->assign("classroom", $classroom);
        return $this->fetch("classroom/updateroom");
    }
    /**
     * 修改数据
     */
    public function update()
    {
        $registrationModel = M("Classroom");
        $validata = new classroomValidate();
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
     * 删除
     */
    public function deleteByIDs()
    {
        $ids = input('ids');
        $ids = rtrim($ids, ",");
        $map['id'] = array('in', $ids);
        $deleteinfo = Classroom::where($map)->delete();
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "删除成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "删除失败！"));
        }
    }
}