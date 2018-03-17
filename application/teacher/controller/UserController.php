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
/**
 * 用户类
 * Class User
 * @package app\admin\controller
 */
class UserController extends Controller
{

    /**
     * 登录验证
     * @return bool
     */
    public function authLogin()
    {
       
        $username = input('UserName');
        $password = input('PassWord');
        if($username==null){
            return json_encode(["status"=>"1","info"=>"*用户名未填写*"]);
        }else if($password==null){
            return json_encode(["status"=>"2","info"=>"*密码未填写*"]);
        }
        
        if (isNotNull($username) && isNotNull($password)) {
            $user = User::get(array("username" => $username, "password" => $password,"typeid"=>"2"));
            if ($user) {
                session('login', $user);
               return json_encode(["status"=>3,"info"=>"登陆成功"]);
            } else {
                session("login", null);
                return json_encode(["status"=>"0","info"=>"*账号或者密码错误*"]);
            }
        } else {
            return 0;
        }
        
    }
    /*
     * 修改密码
     */

    public function modifyData()
    {
        $data['title'] ='密码修改';
        $data['name']="username";
        $data['content']='';
        $this->assign('data',$data);
        return $this->fetch('user/update');
    }


    /**
     * 退出
     */
    public function authLogout()
    {
        session("login", null);
        
    }
    /**
     * 个人信息页
     */
    
    public function personal()
    {
        $id = session("login")["username"];
        $j = "";
        for($i=0;$i<strlen($id);$i++){
            if($id[$i]!="0"){
                $j = $i;
                break;
            }
        }  
        //获取老师的id
        $teacherid= substr($id,$j);
        $join =[
            ["ew_subject subject" ,"subject.id=teacher.teacher_subject_id"],
            ["ew_grade grade ","grade.id = teacher_grade_id"],
            ["ew_campus campus","campus.id = teacher.campusid"]
        ]; 
       $teacher = Db::table("ew_teacher")->alias("teacher")->join($join)->where("teacher.id = {$teacherid} ")->find();
       $this->assign("teacher",$teacher);
       return $this->fetch("user/index");
    }
    /**
     * 修改个人信息
     */

    public function update()
    {
        $user = session('login');
        if(array_key_exists("username",$_POST)){
            Db::name('user')->where('typeid',2)->where("username",$user['username'])->update(['password'=>$_POST['username']]);

            return 1;
        }

    }
}