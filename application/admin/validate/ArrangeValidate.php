<?php
namespace app\admin\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/7
 * Time: 17:28
 */
class ArrangeValidate extends Validate
{
    protected $rule = [
        'schedule_classid' => 'require',
        'schedule_classroomid' => 'require',
        'schedule_starttime' => 'require',
        "schedule_endtime" => 'require',
        'schedule_classbegin' => 'require',
        'schedule_classover' => 'require',
        'schedule_teacherid' => 'require',
        'schedule_perweek' => 'require',
        'schedule_classlength' => 'require|checkRight:无效|checkNum:30',
    ];
    protected $message = [
        'schedule_classid.require'=>'班级不能为空',
        'schedule_classroomid.require'=>'教室不能为空',
        'schedule_starttime.require'=>'开始日期不能为空',
        'schedule_endtime.require'=>'结束日期不能为空',
        'schedule_classbegin.require'=>'上课开始时间不能为空',
        'schedule_classover.require'=>'下课时间不能为空',
        'schedule_teacherid.require'=>'教师不能为空',
        'schedule_perweek.require'=>'请选择上课日',
        'schedule_classlength.require'=>'上课时长不能为空',
        'schedule_classlength.checkRight'=>'请填写正确的上课时间段',
        'schedule_classlength.checkNum'=>'上课时间不能少于30分钟',
    ];

    protected function checkRight($value,$rule)
    {
        return $value != $rule;
    }
    protected function checkNum($value,$rule)
    {
        return $value >= $rule;
    }
}