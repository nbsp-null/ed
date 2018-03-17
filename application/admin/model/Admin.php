<?php
/**
 * Created by PhpStorm.
 * User: mac1
 * Date: 16/6/10
 * Time: 上午10:32
 */

namespace app\admin\model;


use think\Model;

/**
 * 后台管理员
 * Class Admin
 * @package app\admin\model
 */
class Admin extends Model
{
    public function campus()
    {
        return $this->belongsTo('Campus','campusid');
    }
}