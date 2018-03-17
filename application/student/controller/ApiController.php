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

use app\admin\model\Student as StudentModel;

/**
 * Class ApiController
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\student\controller
 *
 * Api接口
 */
class ApiController
{

    /**
     * 获得学生信息
     * @param int $id
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     * Author ghostsf
     * Blog www.ghostsf.com
     */
    public function read($id = 0)
    {
        trace("api read student:" . $id);
        $student = StudentModel::with("grade,course,classes")->select($id);
        if ($student) {
            return json($student);
        } else {
            return json(['error' => '用户不存在'], 404);
        }
    }

}