<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/6/16
 */

namespace app\admin\controller;
use app\admin\model\Grade;
use app\admin\model\Campus;
use app\admin\validate\GradeValidate;
use think\Db;

/**
 * 年级
 * Class GradeController
 * Author mww
 * @package app\admin\controller
 */
class GradeController extends CommonController
{

    /**
     *ajax获得年级JSON
     */
    public function getJSON()
    {
		if($this->redis()){
            if($this->redis->EXISTS('DgetGJSON'))
               return $this->redis->get("DgetGJSON");

        }
		$campusid = session("loginSession")['campusid'];
        $list = Db::name("grade")->where(["campusid"=>$campusid])->select();
		if($this->redis()) {
            $this->redis->set("DgetGJSON",json_encode($list));

        }
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
        $class = Grade::with('campus')->where($_POST)->limit($rows * ($page - 1), $rows)->select();
        $total = Db::name("grade")->where($_POST)->count();
        $data['total'] = $total;
        $data['rows'] = $class;
        return json_encode($data);
    }
    /**
     * 显示添加页
     */
    public function addgrade()
    {
        $campus = Campus::all();
        $this->assign('campus',$campus);
        return $this->fetch("grade/add");
    }
    /**
     * 添加
     */
    public function insert()
    {	
		if($this->redis()){
		if($this->redis->EXISTS('DgetGrade'))
            $this->redis->del("DgetGrade");
		if($this->redis->EXISTS('DgetGJSON'))
            $this->redis->del("DgetGJSON");
	
        }
        $_POST['campusid'] = session('loginSession')['campusid'];
        $registrationModel = M("Grade");
        $validata = new GradeValidate();
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
    public function updategrade()
    {
        $id = input('id', '');
        $grade = Grade::get($id);
        $this->assign("grade", $grade);
        return $this->fetch("grade/update");
    }
    /**
     * 修改
     */
    public function update()
    {	
		if($this->redis()){
		if($this->redis->EXISTS('DgetGrade'))
            $this->redis->del("DgetGrade");
		if($this->redis->EXISTS('DgetGJSON'))
            $this->redis->del("DgetGJSON");
	
        }
        $registrationModel = M("Grade");
        $validata = new GradeValidate();
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
		if($this->redis()){
		if($this->redis->EXISTS('DgetGrade'))
            $this->redis->del("DgetGrade");
		if($this->redis->EXISTS('DgetGJSON'))
            $this->redis->del("DgetGJSON");
	
        }
        $ids = input('ids');
        $ids = rtrim($ids, ",");
        $map['id'] = array('in', $ids);
        $deleteinfo = Grade::where($map)->delete();
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "删除成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "删除失败！"));
        }
    }

}