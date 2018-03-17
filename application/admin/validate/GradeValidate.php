<?php

namespace app\admin\validate;

use think\Validate;
/**
 * Created by PhpStorm.
 * User: niuniu
 * Date: 2016/8/17
 * Time: 10:49
 */
class GradeValidate extends Validate
{
    protected $rule = [
        'grade_name' => 'require',
    ];
    protected $message = [
        'grade_name.require' => '请填写年级名字',
    ];
}