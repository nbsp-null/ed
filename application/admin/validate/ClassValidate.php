<?php
namespace app\admin\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/7
 * Time: 17:28
 */
class ClassValidate extends Validate
{
    protected $rule = [
        'classes_name' => 'require|max:32|unique:classes',
//        'classes_headteacher' => 'require|checknum:1',
        'classes_courseid' => 'require',
//        'classes_lessonteacher' => 'require|checknum:1',
//        'classes_classroomid' => 'require|checknum:1',
        'classes_planstudents' => 'require|number|checknum:10',
    ];
    protected $message = [
        'classes_name.require'=> '名字必须填写',
        'classes_name.max'=> '名字不能超过8个字',
        'classes_name.unique' => '名字不能重复',
//        'classes_headteacher.require'=> '请选择班主任',
//        'classes_headteacher.checknum'=> '请选择班主任',
//        'classes_lessonteacher.require'=> '请选择任课老师',
//        'classes_lessonteacher.checknum'=> '请选择任课老师',
        'classes_courseid.require'=> '请选择课程',
//        'classes_classroomid.require'=> '请选择教室',
//        'classes_classroomid.checknum'=> '请选择教室',
        'classes_planstudents.require'=> '预招人数不能为空',
        'classes_planstudents.number'=> '预招人数必须是数字',
        'classes_planstudents.checknum'=> '预招人数不能低于10人',
    ];

    protected function checknum($value,$rule)
    {
        return $value>=$rule;
    }
}