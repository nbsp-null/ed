<?php

namespace app\admin\validate;

use think\Validate;
/**
 * Created by PhpStorm.
 * User: niuniu
 * Date: 2016/8/17
 * Time: 10:49
 */
class SubjectValidate extends Validate
{
    protected $rule = [
        'subject_name' => 'require',
    ];
    protected $message = [
        'subject_name.require' => '请填写科目名字',
    ];
}