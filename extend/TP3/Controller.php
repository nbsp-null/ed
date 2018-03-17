<?php

namespace TP3;

use think\Response;

class Controller extends \think\Controller {

    /**
     * 系统初始化，将配置信息加入到内存变量中
     *
     */
    protected function _initialize() {
        /* 读取数据库中的配置 */
        $config = S('DB_CONFIG_DATA');
        if (!$config) {
            $config = D('Config')->lists();
            S('DB_CONFIG_DATA', $config);
        }
        C($config); //添加配置
    }

    /**
     * 操作错误跳转的快捷方法
     * @access public
     * @param mixed $msg 提示信息
     * @param string $url 跳转的URL地址
     * @param mixed $data 返回的数据
     * @param integer $wait 跳转等待时间
     * @return mixed
     */
    public function error($msg = '', $url = null, $data = '', $wait = 3) {
        if (IS_AJAX && is_null($url)) {
            $url = '';     //AJAX时将URL初始化一下
        }
        $result = Response::error($msg, $data, $url, $wait);
        Response::isExit(true);     //输出信息后退出
        Response::send($result);    //输出信息
    }

    /**
     * 操作成功跳转的快捷方法
     * @access public
     * @param mixed $msg 提示信息
     * @param string $url 跳转的URL地址
     * @param mixed $data 返回的数据
     * @param integer $wait 跳转等待时间
     * @return mixed
     */
    public function success($msg = '', $url = null, $data = '', $wait = 3) {
        if (IS_AJAX && is_null($url))
            $url = '';     //AJAX时将URL初始化一下
        $result = Response::success($msg, $data, $url, $wait);
        Response::isExit(true);     //输出信息后退出
        Response::send($result);    //输出信息
    }

}
