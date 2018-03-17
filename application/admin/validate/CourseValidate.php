<?php

namespace app\admin\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: niuniu
 * Date: 2016/8/17
 * Time: 10:49
 */
class CourseValidate extends Validate
{
    protected $rule = [
        'course_name' => 'require',
        'course_grade_id' => 'require',
        'course_subject_id' => 'require',
        'course_unitprice' => 'require|number|checkprice:20',
        'course_periodnum' => 'require|number|checknum:1',
        'course_status' => 'require',
    ];
    protected $message = [
        'course_name.require' => '请填写课程名称',
        'course_grade_id.require'=>'年级不能为空',
        'course_subject_id.require'=>'科目不能为空',
        'course_status.require'=>'请选择停用还是启用',
        'course_unitprice.require'=>'价格不能为空',
        'course_unitprice.number'=>'价格必须是数字',
        'course_unitprice.checkprice'=>'价格不能低于20',
        'course_periodnum.require'=>'填写课时数',
        'course_periodnum.number'=>'课时必须是数字',
        'course_periodnum.checknum'=>'课时不能低于1课时',
    ];
    protected function checkprice($value,$rule){
        return $value >= $rule;
    }
    protected function checknum($value,$rule){
        return $value >= $rule;
    }
}