<?php
/**
 * Created by ghostsf
 * Number: n006833
 * Date: 2016/5/31
 */

namespace app\admin\model;


use think\Model;

/**
 * 年级类
 * Class Grade
 * @package app\admin\model
 */
class Subject extends Model
{
    public function campus()
    {
        return $this->belongsTo('Campus','campusid');
    }
}