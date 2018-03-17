<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/6/16
 */

namespace app\admin\controller;
use app\admin\model\Notice;
use app\admin\model\Campus;
use app\admin\validate\NoticeValidate;
use think\Db;
/**
 * 公告
 * Class NoticeController
 * Author mww
 * @package app\admin\controller
 */
class NoticeController extends CommonController
{
    /**
     * 获得数据
     */
    public function getDatas()
    {
        $rows = $_POST['rows'];
        $page = $_POST['page'];
        $start = "";
        if(empty($_POST['time'])){
            $start = 0;
            unset($_POST['time']);
        }else{
            $start = $_POST['time'];
            unset($_POST['time']);
        };
        $_POST = $this->getDataByCampusid($_POST);
        if(isset($_POST['campusid'])){
            $_POST['notice.campusid'] = $_POST["campusid"];
            unset($_POST["campusid"]);
        }
        $class = Notice::with('admin,campus')->where($_POST)->where('time','>',$start)->limit($rows * ($page - 1), $rows)->select();
//        foreach($class as $k=>$v){
//        dump($v['id']);
//    }

        $total = Notice::with('admin,campus')->where($_POST)->where('time','>',$start)->count();
        $data['total'] = $total;
        $data['rows'] = $class;
        return json_encode($data);
    }
    /**
     * 显示添加页
     */
    public function addnotice()
    {
        return $this->fetch("notice/add");
    }
    /**
     * 添加
     */
    public function insert()
    {
            $_POST['campusid'] = session('loginSession')['campusid'];
        $registrationModel = M("Notice");
        $validata = new NoticeValidate();
        if (!$validata->check($_POST)) {
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else {
            $_POST['creator'] = session('loginSession')['id'];
            $_POST['time'] = date("Y-m-d H:i:s");
            $_POST['status'] = 0;
            $registrationModel->insert($_POST);
            $returnData['status'] = 1;
            $returnData['msg'] = "成功";
            return json_encode($returnData);
        }
    }
    /**
     * 显示修改页
     */
    public function updatenotice()
    {
        $id = input('id', '');
        $notice = Notice::get($id);
        $this->assign("notice", $notice);
        return $this->fetch("notice/update");
    }
    /**
     * 修改
     */
    public function update()
    {
        $registrationModel = M("Notice");
        $validata = new NoticeValidate();
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
        $deleteinfo = Notice::where($map)->delete();
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "删除成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "删除失败！"));
        }
    }
    /**
     * 发布公告
     */
    public function commitByIDs()
    {
        $ids = input('ids');
        $ids = rtrim($ids, ",");
        $map['id'] = array('in', $ids);
        $deleteinfo = Notice::where($map)->update(['status'=>1]);
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "发布成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "发布失败！"));
        }
    }
}