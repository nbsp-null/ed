<?php

namespace app\admin\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: niuniu
 * Date: 2016/8/17
 * Time: 10:49
 */
class ConfigValidate extends Validate
{
    protected $rule = [
        'value' => 'require',
    ];
    protected $message = [
        'value.require' => '请填写内容',
    ];
}