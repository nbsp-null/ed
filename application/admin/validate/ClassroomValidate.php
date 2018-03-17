<?php

namespace app\admin\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: niuniu
 * Date: 2016/8/17
 * Time: 10:49
 */
class ClassroomValidate extends Validate
{
    protected $rule = [
        'classroom_name' => 'require',
        'classroom_containnum' => 'require|number|checknum:1',
    ];
    protected $message = [
        'classroom_name.require' => '请填写教室名称',
        'classroom_containnum.require' => '请填写可容纳人数',
        'classroom_containnum.number' => '请填写数字',
        'classroom_containnum.checknum' => '可容纳人数至少1人',
    ];

    protected function checknum($value, $rule)
    {
        return $value >= $rule;
    }
}