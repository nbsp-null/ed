<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/6/16
 */

namespace app\admin\controller;
use app\admin\model\Personalconfig;
use app\admin\validate\SetValidate;
use think\Db;

/**
 * 个性设置
 * Class SetController
 * Author mww
 * @package app\admin\controller
 */
class SetController extends CommonController
{


    /**
     * 修改是否显示动画开关的个性配置
     */
    public function update()
    {
        //表中无数据 先查出所有设置项（默认项的） 去掉id 改下userid 插入数据表
       $user = Db::table("ew_personalconfig")->where('userid',session('loginSession')['id'])->select();
        if(count($user)<1){
            $user = Db::table("ew_config")->where('id','>',129)->select();
            for($a=0;$a<count($user);$a++){
                unset($user[$a]['id']);
                unset($user[$a]['type']);
                $user[$a]['userid'] = session('loginSession')['id'];
            }
            foreach($user as $v){
                Db::table("ew_personalconfig")->insert($v);
            }
        }

        $value = input('value');
        Db::table("ew_personalconfig")->where('userid',session('loginSession')['id'])->where('name','openAnimation')->update(["value"=>$value]);

        $returnData['status'] = 1;
        $returnData['msg'] = "修改成功";
        return json_encode($returnData);
    }
    /**
     * 修改显示页数
     */
    public function updatepage()
    {
        //同上
        $user = Db::table("ew_personalconfig")->where('userid',session('loginSession')['id'])->select();
        if(count($user)<1){
            $user = Db::table("ew_config")->where('id','>',129)->select();
            for($a=0;$a<count($user);$a++){
                unset($user[$a]['type']);
                unset($user[$a]['id']);
                $user[$a]['userid'] = session('loginSession')['id'];
            }
            foreach($user as $v){
                Db::table("ew_personalconfig")->insert($v);
            }
        }
        $wrongmsg = "";
        $validata = new SetValidate();
            unset($_POST['my-checkbox']);   //去掉动画设置的值 影响验证判断
            foreach($_POST as $k=>$v){
                if (!$validata->check(['value'=>$v])) {
                    $wrongmsg .= $validata->getError()."<br/>";
                }
            }
        if($wrongmsg == ""){
            foreach($_POST as $k=>$v){
                Db::table('ew_personalconfig')->where("name",$k)->where("userid",session('loginSession')['id'])->update(['value'=>$v]);
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

}