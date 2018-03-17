<?php
/**
 * Created by ghostsf
 * Date: 2016/4/20
 */

namespace app\admin\controller;


use app\admin\model\Menus;
use think\Controller;

/**
 * 菜单类
 * Class Menu
 * @package app\admin\controller
 */
class MenuController extends CommonController
{
    /**
     * 菜单树
     */
    public function tree()
    {
        //显示启用状态的菜单
        $menus = Menus::all(array("status"=>1));
        $menusArr = array();
        foreach ($menus as $menu) {
            $menu['iconSkin'] = $menu['menu_iconclass'] . " iconskin";
            array_push($menusArr, $menu);
        }
        return json_encode($menusArr);
    }


    /**
     * 修改
     */
    public function update()
    {
        $id = input("menu", -1);
        $menuname = input("menuname", "");
        $menuicon = input("menuicon", "");
        $menu = Menus::get($id);
        $menu->menu_name = $menuname;
        $menu->menu_iconclass = $menuicon;
        return $menu->save();
    }


}