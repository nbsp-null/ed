<?php
/**
 * Created by ghostsf
 * Date: 2016/5/31
 */

namespace app\admin\model;


use think\Model;

/**
 * 公告
 * Class Notice
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\admin\model
 */
class Notice extends Model
{
    public function admin()
    {
        return $this->belongsTo('Admin','creator');
    }
    public function campus()
    {
        return $this->belongsTo('Campus','campusid');
    }
}