<?php
namespace app\admin\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/7
 * Time: 17:28
 */
class StudentValidate extends Validate
{
    protected $rule = [
        'student_name' => 'require|max:20',
        'student_phone' => 'require|max:11|min:11|checkphone:1',
        'student_idcard' => 'max:18|min:18',
        'student_gradeid' => 'require',
        'student_classid' => 'require',
        'student_courseid' => 'require',
        'student_createtime' => 'require',
        'student_sex' => 'require',
    ];
    protected $message = [
        'student_name.require' => '姓名必须填写',
        'student_name.max' => '姓名最多不能超过5个字',
        'student_phone.require' => '电话必须填写',
        'student_phone.max' => '电话最多不能超过11位',
        'student_phone.min' => '电话最低不能少于11位',
        'student_phone.checkphone' => '请填写正确的手机号码格式',
        'student_createtime.require' => '请填写报名日期',
        'student_sex.require' => '请选择性别',
        'student_gradeid.require' => '请选择年级',
        'student_classid.require' => '请选择班级',
        'student_courseid.require' => '请选择课程',
        'student_idcard.max' => '身份证最多不能超过18位',
        'student_idcard.min' => '身份证最多不能低于18位'
    ];
    protected function checkphone($value,$rule)
    {
        return preg_match("/^[1][3578][0-9]{9}$/",$value) == $rule;
    }
}