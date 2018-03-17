<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    //全局变量 正则匹配规则
    '__pattern__' => [
        'name' => '\w+',
        'id' => '\d+',
    ],
	'[hello]'     => [
	':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
	':name' => ['index/hello', ['method' => 'post']],
	],
    //api 路由设置
    '[student]'     => [
        ':id'   => ['student/user/read', ['method' => 'get']],
    ],

];
