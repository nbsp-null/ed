<?php
/**
 * Created by ghostsf
 * Number: n006833
 * Date: 2016/4/19
 */

namespace app\teacher\controller;


use app\admin\model\User;
use think\Controller;
use think\Db;
class NoticeController extends controller{
    /*
     * 公告显示
     */
    public function notice()
    {
      

        $id = session("login")["username"];
        $campusid = session("login")["campusid"];
        $notices = Db::table("ew_notice")->alias("notice")->where(["campusid"=>$campusid,"type"=>3,"status"=>1])->order("time desc")->select();
        $this->assign("notices",$notices);
        return $this->fetch("notice/index");
    }
    /**
     * 显示公告内容
     */
    public function content()
    {
      
        $id = input("id");
        $notice = Db::table("ew_notice")->alias("notice")->where("id = {$id}")->find();
        $this->assign("notice",$notice);
        return $this->fetch("notice/content");
    }
}

