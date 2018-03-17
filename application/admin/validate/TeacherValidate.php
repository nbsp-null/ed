<?php
namespace app\admin\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/7
 * Time: 17:28
 */
class TeacherValidate extends Validate
{
    protected $rule = [
        'teacher_name' => 'require|max:20',
        'teacher_gender'=>'require',
        'teacher_telphone' => 'require|max:11|min:11|checkphone:1',
        'teacher_idcard' => 'require|max:18|min:18',
        'teacher_jobtype'=>'require',
        'teacher_status'=>'require',
        'teacher_subject_id'    =>'require',
        'teacher_grade_id'    =>'require',
        'teacher_joindate'    =>'require',

    ];
    protected $message = [
        'teacher_name.require' => '姓名必须填写',
        'teacher_name.max' => '姓名最多不能超过5个字符',
        'teacher_telphone.require' => '电话必须填写',
        'teacher_telphone.max' => '电话最多不能超过11位',
        'teacher_telphone.min' => '电话最低不能少于11位',
        'teacher_telphone.checkphone' => '请填写正确的手机号码格式',
        'teacher_idcard.require' => '身份证必须填写',
        'teacher_idcard.max' => '身份证最多不能超过18位',
        'teacher_idcard.min' => '身份证最多不能低于18位',
        'teacher_gender.require' => '性别必须选择',
        'teacher_jobtype.require' => '在职类型必须选择',
        'teacher_status.require' => '请选择是否为正式员工',
        'teacher_subject_id.require'      =>'科目必须选择',
        'teacher_grade_id.require' => '年级必须选择',
        'teacher_joindate.require' => '请选择入职日期',
    ];
    protected function checkphone($value,$rule)
    {
        return preg_match("/^[1][3578][0-9]{9}$/",$value) == $rule;
    }
}