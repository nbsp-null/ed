<?php
/**
 * Created by PhpStorm.
 * User: mac1
 * Date: 16/6/10
 * Time: 上午10:37
 */

namespace app\admin\model;


use think\Model;

/**
 * 老师
 * Class Teacher
 * @package app\admin\model
 */
class Teacher extends Model
{
    public function grade()
    {
        return $this->belongsTo('Grade','teacher_grade_id');
    }

    public function subject()
    {
        return $this->belongsTo('Subject','teacher_subject_id');
    }

}