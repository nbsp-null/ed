<?php


namespace app\admin\model;


use think\Model;

/**
 * 学生Model
 * Class Student
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\admin\model
 */
class Student extends Model
{

    // 定义类型转换
   protected $type = [
        'student_createtime'    => 'datetime:Y/m/d',
    ];


    public function grade()
    {
        return $this->belongsTo('Grade','student_gradeid');
    }

    public function course()
    {
        return $this->belongsTo('Course','student_courseid');
    }

    public function classes()
    {
        return $this->belongsTo('Classes','student_classid');
    }
   
}