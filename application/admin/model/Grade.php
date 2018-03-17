<?php
/**
 * Created by ghostsf
 * Date: 2016/5/31
 */

namespace app\admin\model;


use think\Model;

/**
 * 年级类
 * Class Grade
 * @package app\admin\model
 */
class Grade extends Model
{
    public function campus()
    {
        return $this->belongsTo('Campus','campusid');
    }
}