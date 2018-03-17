<?php


namespace TP3;


class Core
{

    // 致命错误捕获
    static public function fatalError()
    {
        if ($e = error_get_last()) {
            if ($e['type'] & C('exception_ignore_type')) {
                return true;
            }
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    ob_end_clean();
                    self::halt(array(), $e);
                    break;
            }
        }
    }

    public static function exception_handle($e)
    {
        $lists = $e->getTrace();
        $errfile = $e->getFile();
        $errno = $e->getCode();
        $errline = $e->getLine();
        $errstr = $e->getMessage();
        //这里得分几种情况来对待
        if ($e instanceof \think\exception\PDOException) {
            $errno = MY_QUERY_ERROR;
            $data = $e->getData();
            $errstr .= ' [ SQL语句 ] : ' . $data['Database Status']['Error SQL'];
        }
        if (!isset($lists[0]['file'])) {
            $lists[0]['file'] = $errfile;
        }
        if (!isset($lists[0]['line'])) {
            $lists[0]['line'] = $errline;
        }
        if (!$errno) {
            $errno = E_USER_WARNING;
        }
        $msg = array(
            'type' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline
        );
        self::halt($lists, $msg);
    }

    /**
     * 生成错误报告
     *
     * @param 错误类型 $errno
     * @param 错误信息 $errstr
     * @param 出错文件 $errfile
     * @param 出错行号 $errline
     * @return 无
     */
    public static function error_handle($errno, $errstr, $errfile, $errline)
    {
        if ($errno & C('exception_ignore_type')) {
            return true;
        }
        if (E_NOTICE == $errno) {
            return true;
        }
        if ($errno == 2048) {
            return true;
        }
        $msg = array(
            'type' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline
        );
        self::halt(array(), $msg);
    }

    /**
     * 隐藏系统路径
     *
     * @param mixed $file
     */
    public static function formatpath($file)
    {
        static $syspath = array();
        if (!$syspath) {
            /**
             * 这里列出一些系统路径，在出错时予以转换以防泄漏系统路径信息
             */
            $ps = array(
                'COMMON_PATH',
                'MODULE_PATH',
                'APP_PATH',
                'LIB_PATH',
                'TPL_PATH',
                'DATA_PATH',
                'FUNC_PATH',
                'ROOT_PATH',
                'RUNTIME_PATH',
                'CORE_PATH',
                'BEHAVIOR_PATH',
                'MODE_PATH',
                'VENDOR_PATH',
                'CONF_PATH',
                'LANG_PATH',
                'HTML_PATH',
                'LOG_PATH',
                'TEMP_PATH',
                'CACHE_PATH',
                'ADDON_PATH',
                'CONTROLLER_PATH',
            );
            foreach ($ps as $k) {
                defined($k) && constant($k) && realpath(constant($k)) ? $syspath[$k] = realpath(constant($k)) . DIRECTORY_SEPARATOR : false;
            }
            arsort($syspath);
        }

        $sc = $re = array();
        foreach ($syspath as $k => $path) {
            $sc[] = $path;
            $re[] = '<font color="red">' . $k . DIRECTORY_SEPARATOR . '</font>';
        }
        if (is_string($file)) {
            $file = str_replace($sc, $re, $file);
        } else if (is_array($file)) {
            foreach ($file as $k => $v) {
                $file[$k] = str_replace($sc, $re, $v);
            }
        }
        return $file;
    }

    /**
     * +----------------------------------------------------------
     * 字符串截取，支持中文和其他编码
     * +----------------------------------------------------------
     * @static
     * @access public
     * +----------------------------------------------------------
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符
     * +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     */
    public static function msubstr($str, $start = 0, $length = 0, $charset = "utf-8", $suffix = true)
    {
        if ($length === 0) {
            $length = strlen($str);
        }
        if (function_exists("mb_substr")) {
            if ($suffix && strlen($str) > ($length - $start)) {
                return mb_substr($str, $start, $length, $charset) . "...";
            } else {
                return mb_substr($str, $start, $length, $charset);
            }
        } elseif (function_exists('iconv_substr')) {
            if ($suffix && strlen($str) > ($length - $start)) {
                return iconv_substr($str, $start, $length, $charset) . "...";
            } else {
                return iconv_substr($str, $start, $length, $charset);
            }
        }
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
        if ($suffix && strlen($str) > ($length - $start)) {
            return $slice . "…";
        }
        return $slice;
    }

    public static function debug_backtrace($lists = array(), $msg = array())
    {
        $skipfunc = $skipfile = $skippath = array();
        if (!$msg || $msg['type'] != 222) {
            $skipfunc = array(
                'include',
                'trigger_error',
                'core::debug_backtrace',
                'core::autoload_handle',
                'core::error_handle',
                'core::exception_handle',
                'cms\core::halt'
            );
            $skipfile = array();
            $skippath = array(
                realpath(C('THINK_PATH')) . DS,
                realpath(ROOT_PATH . DS . 'extend' . DS . 'CMS') . DS
            );
            //pre($skippath);exit;
            foreach ((array)$skipfunc as $k => $v) {
                $skipfunc[$k] = strtolower($v);
            }
        }

        if ($lists) {
            $debug_backtrace = $lists;
        } else {
            $debug_backtrace = debug_backtrace();
        }
        if (!$debug_backtrace) {
            return array('', '');
        }
        krsort($debug_backtrace);

        $lastfile = $line = '';
        $show = '';
        $no = 1;
        foreach ($debug_backtrace as $error) {
            $func = isset($error['class']) ? $error['class'] : '';
            $func .= isset($error['type']) ? $error['type'] : '';
            $func .= isset($error['function']) ? $error['function'] : '';
            //$func = str_replace('CMS\\', '', $func);
            if (!isset($error['line']) || !$error['line']) {
                continue;
            }
            if (in_array(strtolower($func), $skipfunc)) {
                continue;
            }
            //if (substr($error['function'], 0, 2) === '__') {
            //	continue;
            //}
            $file = $error['file'];
            //跳过自定义的文件夹
            $skip = false;
            foreach ($skippath as $path) {
                if (str_replace($path, '', $file) !== $file) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) {
                continue;
            }
            //路过自定义的文件名
            if (in_array(strtolower(basename($file)), $skipfile)) {
                continue;
            }
            if ($file) {
                $lastfile = $error['file'];
                $line = $error['line'];
                $lastfunc = $func;
            }
            $args = array();
            foreach ((array)$error['args'] as $v) {
                $v = htmlspecialchars(self::anytypetostring($v));
                if (strlen($v) > 500) {
                    $v = '<pre>' . self::msubstr($v, 0, 500) . '</pre>';
                }
                $args[] = $v;
            }

            $col = $no % 2 == 0 ? "crowone" : "crowtwo";
            $k = str_replace('~', '&nbsp;', sprintf("%'~4s", $no));
            $v = self::formatpath($file) . "(行号：$error[line], 方法：$func)";
            $show .= sprintf("<li class=\"%s\"><span>%s</span>:%s </li>", $col, $k, $v);
            $no++;
        }
        if ($show) {
            $show = '<h3>引用列表：</h3><ul class="code">' . $show . '</ul>';
        }

        if (!$lastfile) {
            $e = error_get_last();
            if ($e) {
                $lastfile = $e['file'];
                $line = $e['line'];
            }
        }

        /**
         * 生成代码片段
         */
        $lines = file_exists($lastfile) ? file($lastfile) : null;
        if ($lines) {
            if (defined('APP_DEBUG') && constant('APP_DEBUG')) {
                $show .= self::getline($lines, $line, $lastfunc);
            }
        }
        $log = array(
            'File' => self::formatpath($lastfile),
            'Line' => $line,
            'Function' => $lastfunc,
            'Args' => $args
        );
        return array($show, $log);
    }

    public static function halt($lists = array(), $msg = array())
    {
        $msg['message'] = self::formatpath($msg['message']);
        $_sql = '';
        switch ($msg['type']) {
            case MY_QUERY_ERROR:
                $type_title = "SQL 查询错误";
                $bits = explode(" [ SQL语句 ] : ", $msg['message']);
                $msg['message'] = $bits[0];
                if (isset($bits[1])) {
                    $_sql = self::sqlhighlight($bits[1]);
                } else {
                    //这里取到的只是最后一次成功执行的语句，有点不合适
                    $_sql = self::sqlhighlight(\think\DB::getLastSql());
                    //$_sql = \think\Db::getLastSql();
                }
                break;
            case 222:
            case 223:
                $type_title = "开发调试[{$msg['type']}]";
                break;
            default:
                $type_title = self::FriendlyErrorType($msg['type']);//"未知错误[{$msg['type']}]";
        }
        $detail_list = array("<h3><b>" . $type_title . "</b>: {$msg['message']}</h3>\n");
        if ($msg['type'] == 222 || $msg['type'] == 223 || $msg['type'] == MY_QUERY_ERROR || $lists) {
            $files = $lists;
        } else {
            $files = debug_backtrace();
        }

        list($show, $log) = self::debug_backtrace($files, $msg);
        $errorStr = $msg['message'] . '	文件:' . $log['File'] . '	行号:' . $log['Line'] . '	函数:' . $log['Function'];
        if (class_exists('app')) {        //如果是错误引起的，那就是系统运行结束了，不能再调用其它类
            if (C('LOG_RECORD')) {
                \think\Log::write($errorStr, Log::ERR);
            }
        }

        global $_cmsHalt;   //防止再次进入
        if ($_cmsHalt) {
            return true;
        }

        $_SERVER['exception'] = 'fatal';
        ob_end_clean();

        $errmsg = sprintf('在文件 %s 的第 %s 行遇到了一个' . $type_title . '！', $log['File'], $log['Line']);

        $tmp = array();
        if ($msg['type'] == 222) {
            preg_match('@<pre>(.+?)</pre>@isU', ob_get_contents(), $tmp);
            $errmsg .= '<div class="code">' . $tmp[0] . '</div>';
        }
        $detail_list[] = "<div id=\"info\">" . $errmsg . "</div>";
        if ($log['Function']) {
            $v = $log['Function'] . '(' . trim(implode(',', $log['Args'])) . ')';
            $detail_list[] = '<div id="info">调用：' . $v . '</div>';
        }
        if ($_sql) {
            $detail_list[] = "<div id='info'>查询语句:" . $_sql . '</div>';
        }
        if (defined('IS_AJAX') && constant('IS_AJAX')) {
            $msg = sprintf('在文件%s的第%s行出现了问题(%s)，错误信息：%s', $log['File'], $log['Line'], $type_title, $msg['message']);
            $msg = strip_tags($msg);
            $result = \think\Response::error($msg, '', '');
            \think\Response::isExit(true);     //输出信息后退出
            \think\Response::send($result);
        }

        if (defined('APP_DEBUG') && constant('APP_DEBUG')) {
            $detail_list[] = $show;
        }
        $datalist['GET'] = $_GET;
        $datalist['POST'] = $_POST;
        $datalist['COOKIE'] = $_COOKIE;
        //$datalist['Included'] = self::formatpath(get_included_files());
        //$debug = trace();
        //$datalist = array_merge($datalist, $debug);
        foreach ($datalist as $key => $var) {
            if (is_array($var) && count($var)) {
                $detail_list[] = "<h3>" . $key . "数据:</h3><ul class='code'>\n";
                $i = 0;
                foreach ($var as $k => $v) {
                    if ($key == 'SQL') {
                        $v = '语句：' . $v;
                    }
                    $v = trim(self::anytypetostring($v)) . ';';
                    $col = $i % 2 == 0 ? "crowone" : "crowtwo";
                    $i++;
                    if (is_int($k))
                        $k = str_replace('~', '&nbsp;', sprintf("%'~4s", $i)) . ' : ';
                    else
                        $k = trim($k) . ' = ';
                    $detail_list[] = sprintf("<li class=\"%s\"><span>%s</span>%s</li>", $col, $k, $v);
                }
                $detail_list[] = "</ul>\n";
            }
        }
        if ($msg['type'] != '1024' && (defined('APP_DEBUG') && constant('APP_DEBUG') > 1)) {
            $detail_list[] = "<h3>系统环境变量:</h3>\n<ul class='code'>\n";
            $i = 0;
            foreach ($_SERVER as $k => $v) {
                if (strtoupper($k) != $k)
                    continue;
                $v = trim($v);
                $v = preg_replace("/<[a-zA-Z]+>(.*)<\/[a-zA-Z]+>/i", "\\1", $v);
                if (!is_numeric($v))
                    $v = "'" . $v . "'";
                $v .= ";";
                $k = trim($k);
                $col = $i % 2 == 0 ? "crowone" : "crowtwo";
                $i++;
                $detail_list[] = "<li class=\"" . $col . "\"><div>{$k}</div> => <pre>" . $v . "</pre></li>";
            }
            $detail_list[] = "</ul>\n";
        }
        if (defined('APP_DEBUG') && constant('APP_DEBUG')) {

        } else {
            $detail_list = array_slice($detail_list, 0, 1);
        }
        $details = implode("", $detail_list);
        echo self::template($details);
        exit();
    }

    static function getline($var, $line, $func)
    {
        global $_cmsHalt;
        $_cmsHalt = false;
        $result = "<h3>代码片段：</h3><ul class=\"code\">";
        $strlen = sizeof($var);
        $il = strlen($line);
        if (($line % 10) >= 5)
            $il++;
        $i = $line - 5;
        $format = true;
        $spaces = 999999999999;
        $lines = array();
        for (; $i <= $line + 5; ++$i) {
            if ((1 <= $i) && ($i <= $strlen)) {
                $html = @rtrim(@htmlspecialchars($var[$i - 1]));
                $html = str_replace("\t", "    ", $html);
                $lines[$i] = $html;
                $_html = trim($html);
                $spaces = min($spaces, strlen($html) - strlen($_html));
            }
        }

        //删除代码前不必要的空格
        foreach ($lines as $i => $html) {
            $_html = trim($html);
            $_s = strlen($html) - strlen($_html);
            $html = $_html;
            if ($i == $line && $func) {
                $func = str_replace(array('-', '\\'), array('\\-', '\\\\'), $func);
                $_cmsHalt = preg_match('/@[\s]*' . $func . '/U', $html);
                $html = '<b>' . $html . '</b>';
            }
            if ($_s > $spaces) {
                $html = str_repeat(' ', $_s - $spaces) . $html;
            }
            $col = $i % 2 == 0 ? "crowone" : "crowtwo";
            $i = sprintf("%0" . $il . "d", $i);

            if ($i != $line) {
                $result .= "<li class='$col'><div>{$i}</div> <pre>{$html}</pre></li>\n";
            } else {
                $result .= "<li class=\"mark\"><div>" . $i . "</div> <pre>{$html}</pre></li>\n";
            }
        }
        $result .= "</ul>";
        return $result;
    }

    public static function sqlhighlight($sql)
    {
        $sql = preg_replace("/[\s\r\n\t]+/", " ", $sql);
        #格式化
        #关键词
        $keyword = "as|columns|into|in|table|set|from|inner|left|right|join|on|order by|asc|desc|hiving|group|by|limit|where|and|or|\=|>|<";
        $sql = preg_replace_callback("/(\s+\b($keyword)\b\s+)/i", function ($match) {
            return " <font color=blue><b>" . strtoupper($match[1]) . "</b></font> ";
        }, $sql);
        $command = "show|select|insert|update|delete|set";
        $sql = preg_replace_callback("/(\b(\$$command\s+)\b)/i", function ($match) {
            return "<font color=red><b>" . strtoupper($match[1]) . "</b></font> ";
        }, $sql);
        #函数
        $sql = preg_replace("/(\b([a-z0-9_]+))\(/i", "<font color=#FF33FF><b>\\1</b></font>(", $sql);
        #字符串
        $sql = preg_replace("/(([\"']).*?\\2)/", "<font color=red><b>\\1</b></font>", $sql);
        $sql = preg_replace("/=[\s]*<\/b>[\s]*<\/font>[\s]*([0-9]+)/", "=</b></font><font color=red><b>\\1</b></font>", $sql);
        #表前缀
        $sql = str_replace('`', '', $sql);
        $sql = preg_replace_callback('/([\s|\.]+)' . C('database.prefix') . '([a-zA-Z0-9_\-]+)/i', function ($match) {
            return $match[1] . "<font color=green><b>" . parse_name($match[2], 1) . "</b></font>";
        }, $sql);
        $sql = preg_replace_callback('/([\s|\'|"]+)(' . C('database.database') . ')/i', function ($match) {
            return $match[1] . "<font color=green><b>DBNAME</b></font>";
        }, $sql);
        return $sql;
    }

    public static function template($msg)
    {
        $result = "<html>
			<head>
				<title>系统出错</title>
				<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
			</head>
			<style type=\"text/css\">
				body{
					color: #222;
					background: #ccc none;
					font-size: 12px;
					line-height: 150%;
					font-family: Verdana, Arial, Sans-Serif;
					padding: 0 20px 20px 0;
					text-align: left;
				}
				*, html {
					font-size: 12px;
				}
				pre {
					padding:0;
					margin:0;
				}
				a:link,
				a:visited{
					background-color: transparent;
					color: #A80000;
					text-decoration: underline;
				}

				a:hover,
				a:active{
					background-color: transparent;
					color: #D70000;
					text-decoration: underline;
				}

				.code {
					width: auto;
					background: #FFF;
					border: 1px solid #ddd;
					margin: 5px;
					padding: 3px 5px 3px 3px;
					list-style: none;
				}

				.code li {
					clear: both;
					padding:0 5px;
					margin:2px 0;
				}
				.code li div, .code li pre {
					display: inline;
					word-wrap: break-word;
					font-family: Verdana, Arial, Sans-Serif;
				}
				li.crowone {
					background:#FcFcFc;
				}

				li.crowtwo {
					background:#F0F0F0;
				}

				li.mark {
					background: #FEFCD3;
					color: #FF0000;
				}
				#wrapper {
					max-width: 1000px;
                    min-width: 480px;
					border: 2px solid #BBB;
					background: #FFF none;
					margin: 10px auto;
					padding-bottom:5px;
					box-shadow: 10px 10px 5px #888888;
				}

				#info {
					border:1px solid #f0f0f0;
					background: #F7F7F7 none;
					color: #888;
					font-size: 12px;
					margin: 5px 5px 0 5px;
					padding: 5px;
                    word-break: break-all;
				}

				h1 {
					font: normal bold 14px/200% Verdana, sans-serif;
					padding: 3px 5px;
					margin: 0;
					background: #F8F8F8 none;
					border-bottom: 1px solid #CCC;
				}

				h3 {
					font: normal normal 12px/150% Arial, sans-serif;
					margin: 5px;
					color: #A80000;
					border-bottom: 1px solid #EEE;
                    word-break: break-all;
				}
			</style>
			<body>
				<div id=\"wrapper\">
					<h1>发生如下错误:</h1>" . $msg . "
				</div>
			</body></html>";
        return $result;
    }

    /**
     * 转换参数为字符串
     *
     * @param anytype $data 要转换的数据，目前主要是针对数组、对象
     * @return string 转换后的数据，主要用来进行错误报告的处理
     */
    static function anytypetostring($data = '')
    {
        if ($data) {
            if (is_string($data)) {
                return $data;
            } else {
                return print_r($data, 1);
            }
        } else {
            return '{EMPTY}';
        }
    }

    static function FriendlyErrorType($type)
    {
        //ThinkPHP 5的一些代码及含义
        $think_code = array(
            10000 => '控制器名称不合法',
            10001 => '类文件不存在',
            10002 => '操作不存在',
            10004 => '缺少参数',
            10005 => '模块不存在',
            10006 => '应用程序文件夹不可写',
            10007 => '自定义加载类不存在',
            10008 => '调度失败',
            10700 => '模板文件不存在',
            11602 => '缓存写入失败',
            11700 => 'SESSION操作失败',
            11600 => '不支持的标签',
            10300 => '数据类型错误',
        );
        if (isset($think_code[$type])) {
            return $think_code[$type];
        }
        $return = "";
        if ($type & E_ERROR) // 1 //
            $return .= '& E_ERROR ';
        if ($type & E_WARNING) // 2 //
            $return .= '& E_WARNING ';
        if ($type & E_PARSE) // 4 //
            $return .= '& E_PARSE ';
        if ($type & E_NOTICE) // 8 //
            $return .= '& E_NOTICE ';
        if ($type & E_CORE_ERROR) // 16 //
            $return .= '& E_CORE_ERROR ';
        if ($type & E_CORE_WARNING) // 32 //
            $return .= '& E_CORE_WARNING ';
        if ($type & E_COMPILE_ERROR) // 64 //
            $return .= '& E_COMPILE_ERROR ';
        if ($type & E_COMPILE_WARNING) // 128 //
            $return .= '& E_COMPILE_WARNING ';
        if ($type & E_USER_ERROR) // 256 //
            $return .= '& E_USER_ERROR ';
        if ($type & E_USER_WARNING) // 512 //
            $return .= '& E_USER_WARNING ';
        if ($type & E_USER_NOTICE) // 1024 //
            $return .= '& E_USER_NOTICE ';
        if ($type & E_STRICT) // 2048 //
            $return .= '& E_STRICT ';
        if ($type & E_RECOVERABLE_ERROR) // 4096 //
            $return .= '& E_RECOVERABLE_ERROR ';
        if ($type & E_DEPRECATED) // 8192 //
            $return .= '& E_DEPRECATED ';
        if ($type & E_USER_DEPRECATED) // 16384 //
            $return .= '& E_USER_DEPRECATED ';
        return substr($return, 2);
    }

}
