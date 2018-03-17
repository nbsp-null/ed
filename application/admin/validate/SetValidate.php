<?php

namespace app\admin\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: niuniu
 * Date: 2016/8/17
 * Time: 10:49
 */
class SetValidate extends Validate
{
    protected $rule = [
        'value' => 'require|number|checknum:2',
    ];
    protected $message = [
        'value.require' => '请填写页数',
        'value.number' => '必须是数字',
        'value.checknum' => '不能小于5条',
    ];
    protected function checknum($value, $rule)
    {
        return $value >= $rule;
    }
}