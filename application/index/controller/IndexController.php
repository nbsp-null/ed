<?php
namespace app\index\controller;

<<<<<<< HEAD:application/index/controller/IndexController.php

=======
>>>>>>> bf886ba04e664bd76f5b3e55256d8869b8abcd71:application/index/controller/IndexController.php
class IndexController
{

    /**
     * 下载安装引导
     * @return string
     * Author ghostsf
     */
    public function index()
    {
        /**
         *step1:同意使用协议 同意继续 不同意中断
         *step2:数据库配置设置 设置总管理账号密码等信息 检测数据库是否能连接
         *step3:初始化数据库 导入数据库结构sql初始化数据库
         *step4:安装完成 跳转到admin模块
         */

        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }


}
