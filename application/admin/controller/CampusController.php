<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/6/16
 */

namespace app\admin\controller;

use app\admin\model\Campus;
use app\admin\validate\CampusValidate;
use think\Db;

/**
 * 校区
 * Class CampusController
 * Author mww
 * @package app\admin\controller
 */
class CampusController extends CommonController
{

    /**
     *ajax获得校区JSON
     * Author ghostsf
     */
    public function getJSON()
    {
        $list = Campus::all();
        return json_encode($list);
    }

    /**
     * 获得数据
     */
    public function getDatas()
    {
        $rows = $_POST['rows'];
        $page = $_POST['page'];
        $_POST = $this->getDataByCampusid($_POST,"id");
        if(session("loginSession")['typeid']==0){
            unset($_POST["id"]);
        }else{
            $_POST['admin.id'] = $_POST["id"];
            unset($_POST["id"]);
        }
        $class = Campus::where($_POST)->limit($rows * ($page - 1), $rows)->select();
        $total = Campus::where($_POST)->count();
        $data['total'] = $total;
        $data['rows'] = $class;
        return json_encode($data);
    }
    /**
     * 显示添加页
     */
    public function addcampus()
    {
        return $this->fetch("campus/add");
    }
    /**
     * 添加数据
     */
    public function insert()
    {
        $registrationModel = M("Campus");
        $validata = new CampusValidate();
        if (!$validata->check($_POST)) {
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else {
            $registrationModel->insert($_POST);
            $returnData['status'] = 1;
            $returnData['msg'] = "添加成功";
            return json_encode($returnData);
        }
    }
    /**
     * 显示修改页
     */
    public function updatecampus()
    {
        $id = input('id', '');
        $Campus = Campus::get($id);
        $this->assign("campus", $Campus);
        return $this->fetch("campus/update");
    }
    /**
     * 修改
     */
    public function update()
    {
        $registrationModel = M("Campus");
        $validata = new CampusValidate();
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
        $deleteinfo = Campus::where($map)->delete();
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "删除成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "删除失败！"));
        }
    }

}