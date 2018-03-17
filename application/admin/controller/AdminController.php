<?php
/**
 * Created by PhpStorm.
 * User: mac1
 * Date: 16/6/10
 * Time: 上午10:32
 */

namespace app\admin\controller;
use app\admin\model\Admintype;
use app\admin\model\Campus;
use app\admin\validate\AdminValidate;
use think\Db;
use app\admin\model\Admin;

class AdminController extends CommonController
{
    /**
     * 登录验证
     * @return bool
     */
    public function authLogin()
    {
        $username = input('UserName');
        $password = input('PassWord');
        if (isNotNull($username) && isNotNull($password)) {
            $admin = Admin::get(array("username" => $username, "password" => $password));
            /* if ($admin['campusid'] != 1) {
                return 0;
            } */
            if ($admin) {
                session('loginSession', $admin);
                return 1;
            } else {
                session("loginSession", null);
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * 退出
     */
    public function authLogout()
    {
        session("loginSession", null);
        return 1;
    }

    /**
     * 个人信息
     */
    public function personinfo_manage()
    {
        $admin = Db::table('ew_admin')->where('id', session('loginSession')['id'])->find();
        $this->assign('admin', $admin);
        return $this->fetch('admin/personinfo');
    }

    /**
     * 修改个人信息
     */
    public function personal_setting()
    {
        $admin = Db::table('ew_admin')->where('id', session('loginSession')['id'])->find();
        $admin['regtime'] = substr($admin['regtime'], 0, 10);
        $admin['campusid'] = session('loginSession')['campusid'];
        $this->assign('admin', $admin);
        return $this->fetch('admin/setting');
    }

    public function updateSetting()
    {
        $registrationModel = M("Admin");
        $validata = new AdminValidate();
        if (!$validata->check($_POST)) {
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else {
            unset($_POST['campusid']);
            $registrationModel->update($_POST);
            $returnData['status'] = 1;
            $returnData['msg'] = "修改成功";
            return json_encode($returnData);
        }
    }

    /**
     * 修改密码
     */
    public function change_password()
    {
        $admin = Db::table('ew_admin')->where('id', session('loginSession')['id'])->find();
        $this->assign('admin', $admin);
        return $this->fetch('admin/password');
    }

    public function updatePassword()
    {
        $registrationModel = M("Admin");
        $admin = Db::table('ew_admin')->where('id', session('loginSession')['id'])->find();
        if ($_POST['oldPassword'] != $admin['password']) {
            $returnData['status'] = 0;
            $returnData['msg'] = '原密码输入不正确';
            return json_encode($returnData);
        } else if ($_POST['password'] != $_POST['surePassword']) {
            $returnData['status'] = 0;
            $returnData['msg'] = '两次密码输入不一样';
            return json_encode($returnData);
        } else {
            unset($_POST['oldPassword']);
            unset($_POST['surePassword']);
            $registrationModel->update($_POST);
            $returnData['status'] = 1;
            $returnData['msg'] = "修改成功";
            return json_encode($returnData);
        }
    }

    /**
     * 获得数据
     * Author ghostsf
     * Blog www.ghostsf.com
     */
    public function getDatas()
    {
        $rows = $_POST['rows'];
        $page = $_POST['page'];
        $start = "";
        if (empty($_POST['regtime'])) {
            $start = 0;
            unset($_POST['regtime']);
        } else {
            $start = $_POST['regtime'];
            unset($_POST['regtime']);
        };
        $searchPath = $this->getDataByCampusid($_POST);
        if (session("loginSession")['typeid'] == 0) {
            unset($searchPath["campusid"]);
        } else {
            $searchPath['admin.campusid'] = $searchPath["campusid"];
            unset($searchPath["campusid"]);
        }
        $class = Admin::with("campus")->where($searchPath)->where('regtime', '>', $start)->limit($rows * ($page - 1), $rows)->select();

        $total = Admin::with("campus")->where($searchPath)->where('regtime', '>', $start)->count();
        $data['total'] = $total;
        $data['rows'] = $class;
        return json_encode($data);
    }

    public function addadmin()
    {
        $campus = Campus::all();
        $this->assign('campus', $campus);
        $this->assign('user', session('loginSession')['typeid']);
        return $this->fetch("admin/add");
    }

    public function insert()
    {
        if (isNotNull(session('loginSession')['campusid'])) {
            $_POST['campusid'] = session('loginSession')['campusid'];
        }
        $registrationModel = M("Admin");
        $validata = new AdminValidate();
        if (!$validata->check($_POST)) {
            $returnData['status'] = 0;
            $returnData['msg'] = $validata->getError();
            return json_encode($returnData);
        } else {
            $_POST['regtime'] = date("Y-m-d H:i:s");
            $_POST['typeid'] = 1;
            $registrationModel->insert($_POST);
            $returnData['status'] = 1;
            $returnData['msg'] = "成功";
            return json_encode($returnData);
        }
    }

    public function updateadmin()
    {
        $id = input('id', '');
        $User = Admin::get($id);
        $this->assign("user", $User);
        $campus = Campus::all();
        $this->assign('campus', $campus);
        $this->assign('admin', session('loginSession')['typeid']);
        return $this->fetch("admin/update");
    }

    public function update()
    {
        $registrationModel = M("Admin");
        $validata = new AdminValidate();
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
        $deleteinfo = Admin::where($map)->delete();
        if ($deleteinfo) {
            return json_encode(array("status" => 1, "msg" => "删除成功！"));
        } else {
            return json_encode(array("status" => 0, "msg" => "删除失败！"));
        }
    }

    /**
     * @return mixed
     */
    public function ghname()
    {
        $user = Db::name('campus')->where('id=1')->find();
        $this->assign("name", $user['campus_name']);
        $this->assign("id", $user['id']);
        return $this->fetch("admin/dd");
    }

    /**
     * @return string
     * @throws \think\Exception
     */

    public function mark()
    {
        $id = input("id");
        $name = trim(input("name"));

        M("campus")->where(["id" => $id])->update(["campus_name" => $name]);
        return json_encode(["status" => 1, "msg" => '修改成功']);
    }

    /**
     * 公告
     */
    public function gonggao()
    {
        $id = input('id');
        $user = Db::name('notice')->where("id={$id}")->find();
        $this->assign("user", $user);
        return $this->fetch("admin/gonggao");
    }

    /**
     * 公告列表页
     */
    public function gonggaolist()
    {
        $campusid = session("loginSession")['campusid'];
        $num = M("notice")->where(["campusid" => $campusid, "type" => '1', "status" => '1'])->count('id');
        $p=10;
        $max=ceil($num/$p);
        //$_POST
        if(!isset($_POST['p'])) $_POST['p']=1;
        if($_POST['p']<1){$_POST['p']=1;}
        if($_POST['p']>$max){$_POST['P']=$max;}
        $prev=$_POST['p']==1?1:$_POST['p']-1;
        $next=$_POST['p']==$max?$max:$_POST['p']+1;
        $start=($_POST['p']-1)*$p;

        $data = Db::name("notice")->where(["campusid" => $campusid, "type" => '1', "status" => '1'])->order("time desc")->limit("{$start},{$p}")->select();

        $this->assign("datas", $data);
        $this->assign("prev",$prev);
        $this->assign("next",$next);
        $this->assign("max",$max);
        return $this->fetch("admin/list");
    }

    /**
     * 公告列表页
     */

    /**
     * 修补alert
     */
    public function alertlog()
    {
        $ti=input("t");
        if($ti==1){
            $tishi="请选取表格";
        }elseif($ti==2){
            $tishi="导入成功";
        }elseif($ti==3){
            $tishi="只限上传excel表格！";
        }elseif($ti==4){
            $tishi="对不起，选取不能为空";
        }elseif($ti==5){
            $tishi="此功能暂时不对外开放<br/>例子：今天:8点半约张总吃饭";
        }
        $this->assign("ti",$tishi);
        return $this->fetch("admin/alert");
    }
    /**
     * 日历事件页面
     */

    public  function event()
    {
        return $this->fetch("admin/event");
    }




}