<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/6/16
 */

namespace app\admin\controller;
use app\admin\model\Subject;
use app\admin\model\Campus;
use app\admin\validate\SubjectValidate;
use think\Db;
/**
 * 科目 Controller
 * Class SubjectController
 * Author mww
 * @package app\admin\controller
 */
class SubjectController extends CommonController
{

    /**
     *ajax获得科目JSON
     */
    public function getJSON()
    {	
		if($this->redis()){
            if($this->redis->EXISTS('DgetJSON'))
               return $this->redis->get("DgetJSON");

        }
        $campusid = session("loginSession")['campusid'];
        $list = Subject::where(["campusid"=>$campusid])->select();
		if($this->redis()) {
            $this->redis->set("DgetJSON",json_encode($list));

        }
        return json_encode($list);
    }
    /**
     * 获得数据
     * Author mww
     * Blog www.ghostsf.com
     */
    public function getDatas()
    {
        $rows = $_POST['rows'];
        $page = $_POST['page'];
        $_POST = $this->getDataByCampusid($_POST);
        $class = Subject::with('campus')->where($_POST)->limit($rows * ($page - 1), $rows)->select();
        $total = Subject::where($_POST)->count();
        $data['total'] = $total;
        $data['rows'] = $class;
        return json_encode($data);
    }
    public function addsubject()
    {
        return $this->fetch("subject/addsubject");
    }
    public function insert()
    {
		if($this->redis()){
            if($this->redis->EXISTS('DgetJSON'))
               $this->redis->del("DgetJSON");

        }
            $_POST['campusid'] = session('loginSession')['campusid'];
        $registrationModel = M("Subject");
        $validata = new SubjectValidate();
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
    public function updatesubject()
    {
        $id = input('id', '');
        $subject = Subject::get($id);
        $this->assign("subject", $subject);
        return $this->fetch("subject/updatesubject");
    }
    public function update()
    {		
		if($this->redis()){
            if($this->redis->EXISTS('DgetJSON'))
               $this->redis->del("DgetJSON");

        }
        $registrationModel = M("Subject");
        $validata = new subjectValidate();
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
		if($this->redis()){
            if($this->redis->EXISTS('DgetJSON'))
               $this->redis->del("DgetJSON");
        }
        $ids = input('ids');
        $ids = rtrim($ids, ",");
        $map['id'] = array('in', $ids);
        $deleteinfo = Subject::where($map)->delete();
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "删除成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "删除失败！"));
        }
    }

}