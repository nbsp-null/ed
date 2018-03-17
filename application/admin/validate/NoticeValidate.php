<?php

namespace app\admin\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: niuniu
 * Date: 2016/8/17
 * Time: 10:49
 */
class NoticeValidate extends Validate
{
    protected $rule = [
        'title' => 'require|max:20|min:2',
        'contents' => 'require',
        'type' => 'number'
    ];
    protected $message = [
        'title.require' => '请填写公告名称',
        'title.max' => '名称不得超过20个字符(不区分中英文)',
        'title.min' => '公告名称不得小于2个字符(不区分中英文)',
        'contents.require' => '请填写公告内容',
        'type.number' => '请填写数字'
    ];
}