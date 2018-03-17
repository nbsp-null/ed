<?php
namespace app\admin\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/7
 * Time: 17:28
 */
class AdminValidate extends Validate
{
    protected $rule = [
        'username' => 'require|max:20|checkuser:1|unique:admin',
		'password' =>'require|max:11|min:6',
        'name' => 'require|max:20',
        'phone' => 'require|max:11|min:11|checkphone:1',
        'mail' => 'email',
        "campusid" => "checkcam:1",
		
    ];
    protected $message = [
        'username.require'=> '用户名必须填写',
        'username.max'=> '用户名不能超过5个字',
		'password.require' =>'密码不能为空',
		'password.min' =>"密码不能少于6位",
		'password.max' =>'密码不能多于11位',
        'name.require' => '姓名必须填写',
        'name.max' => '姓名最多不能超过5个字',
        'phone.require' => '手机号码必须填写',
        'phone.max' => '手机号码最多不能超过11位',
        'phone.min' => '手机号码最低不能少于11位',
        'phone.checkphone' => '请填写正确的手机号码格式',
        'mail.email' => '邮箱格式不正确',
        'username.checkuser' => '用户名不符合要求',
        'username.unique' => '此用户名已存在',
        "campusid.checkcam" =>'请选择所在校区'
    ];
    protected function checkuser($value,$rule)
    {
       return preg_match("/^[a-zA-Z]+[a-zA-Z0-9]*$/",$value) == $rule;
    }
    protected function checkphone($value,$rule)
    {
        return preg_match("/^[1][3578][0-9]{9}$/",$value) == $rule;
    }
    protected function checkcam($value,$rule)
    {
        return $value>=$rule;
    }
}