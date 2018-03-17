<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/6/16
 */

namespace app\admin\controller;
use app\admin\validate\ConfigValidate;
use app\admin\validate\ConfignumValidate;
use think\Db;

/**
 * 系统配置
 * Class ConfigController
 * Author mww
 * @package app\admin\controller
 */
class ConfigController extends CommonController
{
    /**
     * 修改
     */
    public function update()
    {
        $wrongmsg = "";
        foreach($_POST as $k=>$v){
            $validata = new ConfigValidate();
            $validatanum = new ConfignumValidate();
            if($k=='openAnimation' || $k=='coursegridsize' || $k=='studentgridsize' || $k=='teachergridsize'){
                if (!$validatanum->check(['value'=>$v])) {
                    $wrongmsg .= $validatanum->getError()."<br/>";
                }
            }
            if (!$validata->check(['value'=>$v])) {
                $wrongmsg .= $validata->getError()."<br>";
            }

        }
        if($wrongmsg == ""){
            unset($_POST['my-checkbox']);
            foreach($_POST as $k=>$v){
                Db::table('ew_config')->where("name",$k)->update(['value'=>$v]);
            }
            $returnData['status'] = 1;
            $returnData['msg'] = "修改成功";
            return json_encode($returnData);
        }else{
            $returnData['status'] = 0;
            $returnData['msg'] = $wrongmsg;
            return json_encode($returnData);
        }
    }
    //修改动画开关的默认配置
    public function updateAnimation()
    {
        $value = input('value');
        Db::table("ew_config")->where('name','openAnimation')->update(["value"=>$value]);
        $returnData['status'] = 1;
        $returnData['msg'] = "修改成功";
        return json_encode($returnData);
    }
}