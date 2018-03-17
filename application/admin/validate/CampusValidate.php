<?php

namespace app\admin\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: niuniu
 * Date: 2016/8/17
 * Time: 10:49
 */
class CampusValidate extends Validate
{
    protected $rule = [
        'campus_name' => 'require',
        'campus_address' => 'require',
    ];
    protected $message = [
        'campus_name.require' => '请填写分区名称',
        'campus_address.require' => '请填写分区地址',
    ];

}