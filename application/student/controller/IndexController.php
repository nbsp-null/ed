<?php
// +----------------------------------------------------------------------
// | Unpor
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.unpor.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: ghostsf <ghost_sf@163.com>
// +----------------------------------------------------------------------
// | AuthorBlog: http://www.ghostsf.com
// +----------------------------------------------------------------------
// | Date: 2016/9/23
// +----------------------------------------------------------------------

namespace app\student\controller;


use think\Controller;

/**
 * 主入口
 * Class IndexController
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\student\controller
 */
class IndexController extends Controller
{
    /**
     * 登录页面
     * Author ghostsf
     */
    public function login(){
        return $this->fetch("login");
    }

    public function index(){
        $user = session('loginStudent');
        if($user){
            return $this->fetch("student");
        }else{
            return $this->fetch("login");
        }
        
    }



}