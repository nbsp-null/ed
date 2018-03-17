<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/7/12
 */

namespace app\admin\controller;

/**
 * Modal 中转
 * Class ModalController
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\admin\controller
 */
class ModalController extends CommonController
{
    public function index($name = "")
    {
        return $this->fetch("modal/" . $name);
    }

}