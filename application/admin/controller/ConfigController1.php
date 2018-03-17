<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/6/16
 */

namespace app\admin\controller;
use app\admin\model\Config;
use app\admin\validate\ConfigValidate;
use think\Db;

/**
 * 配置
 * Class GradeController
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\admin\controller
 */
class ConfigController extends CommonController
{


    /**
     * 获得数据
     * Author ghostsf
     * Blog www.ghostsf.com
     */
    public function getDatas()
    {
        foreach ($_POST as $key=>$value){
            if($key=='page'){
                $page =  $value;
                unset($_POST[$key]);
            }  else if($key=='rows'){
                $rows =  $value;
                unset($_POST[$key]);
            }else if ($value=='') {
                unset($_POST[$key]);
            }else{
                $_POST[$key]=Array('like','%'.$value.'%');
            }
        }
        $class = Config::where($_POST)->limit($rows * ($page - 1), $rows)->select();
        $total = Config::where($_POST)->count();
        $data['total'] = $total;
        $data['rows'] = $class;
        return json_encode($data);
    }

    public function updateconfig()
    {
        $id = input('id', '');
        $Config = Config::get($id);
        $this->assign("config", $Config);
        return $this->fetch("config/update");
    }
    public function update()
    {
            $registrationModel = M("Config");
            $validata = new ConfigValidate();
            if (!$validata->check($_POST)) {
                $returnData['status'] = 0;
                $returnData['msg'] = $validata->getError();
                return json_encode($returnData);
            } else {
                $registrationModel->update($_POST);
                $returnData['status'] = 1;
                $returnData['msg'] = "修改成功";
                return json_encode($returnData);
            }
        }
}