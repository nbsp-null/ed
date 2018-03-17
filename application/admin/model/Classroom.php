<?php
/**
 * Created by ghostsf
 * Date: 2016/5/31
 */

namespace app\admin\model;


use think\Model;

/**
 * 教室类
 * Class Classroom
 * @package app\admin\model
 */
class Classroom extends Model
{
    public function campus()
    {
        return $this->belongsTo('Campus','campusid');
    }
}