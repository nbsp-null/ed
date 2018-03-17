<?php


namespace TP3;

use think\Config;

class Template
{

    // 全局的
    private $vars = array(); //变量表
    var $subtemplates = array();
    var $csscurmodules = '';
    var $replacecode = array('search' => array(), 'replace' => array());
    var $file = '';
    public $tpldir = 'default';
    public $tplpath = '';
    public $objdir = '';
    var $suffix = '.html';

    function __construct($config = array())
    {
        $this->tplpath = $config['view_path'];
        $this->tpldir = $config['default'] ?: 'default';
        $this->suffix = $config['view_suffix'] ?: '.html';
        $this->objdir = CACHE_PATH . 'View' . DIRECTORY_SEPARATOR;
        $this->objdir = Files:: formatpath($this->objdir, false);
        if (!is_dir($this->objdir)) {
            Files:: autocreatePath($this->objdir, 0777);
        }
        if (version_compare(PHP_VERSION, '5') == -1) {
            register_shutdown_function(array(&$this, '__destruct'));
        }
    }

    /**
     * 定义模板中要用到的变量的值
     *
     * @param 变量名 $key
     * @param 值 $value
     */
    function assign($key, $value = '')
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->assign_value($k, $v);
            }
        } else {
            $this->assign_value($key, $value);
        }
    }

    public function assign_value($k, $v)
    {
        $this->vars[$k] = $v;
    }

    function __destruct()
    {
        global $_G;
        if (isset($_SERVER['exception']) || constant('IS_AJAX') || (defined('IN_ADMINCP') && constant('IN_ADMINCP')))
            return;
        $content = ob_get_contents();
        ob_clean();
        //这里可以做一系列的处理，比如替换某些关键字什么地
        echo $content;
    }

    public function display($file, $json = array())
    {
        global $_G;
        $file = strtolower($file);
        extract($this->vars, EXTR_SKIP);
        $this->file = $file;
        include $this->gettpl($file);
    }

    public function getTplFile($file)
    {
        $tpldir = $this->tpldir;
        $tplfile = $this->tplpath . $tpldir . '/' . $file . $this->suffix;
        if (!file_exists($tplfile)) {
            $tpldir = 'default';
            $tplfile = $this->tplpath . $tpldir . '/' . $file . $this->suffix;
        }
        $tplfile = Files::formatpath($tplfile, false);
        if (!file_exists($tplfile)) {
            //不存在模板文件
            E(sprintf('Sorry, the template file %s is not found!', Files::formatpath($tplfile)), E_USER_ERROR);
        } else {
            if (IS_WIN && APP_DEBUG) {
                if (basename(realpath($tplfile)) != basename($tplfile)) {
                    E('文件名大小写不正确:' . $tplfile, E_USER_ERROR);
                }
            }
        }
        return $tplfile;
    }

    function getTpl($file)
    {
        $tplfile = $this->getTplFile($file);
        $objfile = $this->objdir . str_replace(array(DS, '/'), "@", ($this->tpldir . '/' . $file)) . '.php';
        if (!file_exists($objfile) || @filemtime($objfile) < filemtime($tplfile)) {
            $s = $this->complie($tplfile, $file);
            // 此处不锁，多个进程并发写入可能会有问题。// PHP 5.1 以后加入了 LOCK_EX 参数
            file_put_contents($objfile, $s);
        }
        return $objfile;
    }

    public function complie($tplfile, $file)
    {
        if (defined('APP_DEBUG') && constant('APP_DEBUG'))
            $_SERVER['tpls'][] = $tplfile;
        $template = file_get_contents($tplfile);

        $var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\-\>)?[a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
        $const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";

        $template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);

        $template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
        $template = preg_replace_callback("/[\n\r\t]*\{date\((.+?)\)\}[\n\r\t]*/i", array($this, 'datetags'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{avatar\((.+?)\)\}[\n\r\t]*/i", array($this, 'avatartags'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{eval\s+(.+?)\s*\}[\n\r\t]*/is", array($this, 'evaltags'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{CustomStyle\}[\n\r\t]*/is", array($this, 'processcustom'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{CustomScript\}[\n\r\t]*/is", array($this, 'processcustom'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{:([a-zA-Z0-9\-_]+)\((.+?)\)\}[\n\r\t]*/i", array($this, 'customfunc'), $template);
        $template = str_replace("{LF}", "<?=\"\\n\"?>", $template);
        $template = preg_replace("/\{(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
        $template = preg_replace_callback("/$var_regexp/s", array($this, 'addquote'), $template);
        $template = preg_replace_callback("/\<\?\=\<\?\=$var_regexp\?\>\?\>/s", array($this, 'addquote'), $template);

        $template = preg_replace_callback("/[\n\r\t]*\{subtemplate\s+(.+?)\}[\n\r\t]*/is", array($this, 'subtemplatetags'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{template\s+([a-z0-9_:\/]+)\}[\n\r\t]*/is", array($this, 'templatetags'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is", array($this, 'templatetags'), $template);

        $headeradd = '';
        if (!empty($this->subtemplates)) {
            $headeradd .= "\n0";
            foreach ((array)$this->subtemplates as $fname) {
                $headeradd .= " || checktplrefresh('$file', '$fname')";
            }
            $headeradd .= ';';
        }

        $template = "<?php if(!defined('THINK_PATH')) exit('Access Denied'); {$headeradd}?>\n$template";

        $template = preg_replace_callback("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/is", array($this, 'echotags'), $template);
        $template = preg_replace_callback("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/is", array($this, 'iftags'), $template);
        $template = preg_replace_callback("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/is", array($this, 'elseiftags'), $template);
        $template = preg_replace("/\{else\}/i", "<? } else { ?>", $template);
        $template = preg_replace("/\{\/if\}/i", "<? } ?>", $template);
        $template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", array($this, 'looptags'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", array($this, 'looptags'), $template);
        $template = preg_replace("/\{\/loop\}/i", "<? } ?>", $template);
        $template = preg_replace("/\{$const_regexp\}/s", "<?=\\1?>", $template);
        if (!empty($this->replacecode)) {
            $template = str_replace($this->replacecode['search'], $this->replacecode['replace'], $template);
        }
        //$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);
        //$template = preg_replace("/ \?\>[\n\r]*\<\?php /s", " ", $template);

        $template = preg_replace_callback("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/", array($this, 'transamp'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{tpl\s+([a-zA-Z0-9_\[\]]+)\}(.+?)\{\/tpl\}/is", array($this, 'striptpl'), $template);
        //$template = preg_replace_callback("/\<script[^\>]*?src=\"(.+?)\"(.*?)\>\s*\<\/script\>/is", array($this, 'stripscriptamp'), $template);
        //$template = preg_replace_callback("/[\n\r\t]*\{block\s+([^\}]+?)\}[\n\r\t]*/is", array($this, 'blocktags'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{url\s*\(([^\)]+?)\)\s*\}[\n\r\t]*/is", array($this, 'urltags'), $template);

        $template = preg_replace('/\\\{(.+?)\\\}/is', "{\\1}", $template);
        $template = str_replace('".<?=', '".', $template);
        $template = str_replace('?>."', '."', $template);

        // 替换短标记
        $template = preg_replace_callback("/\<\?\=$var_regexp\?\>/s", array($this, 'addquote'), $template);
        $template = str_replace('<?=', '<?php echo ', $template);
        $template = str_replace('<? ', '<?php ', $template);

        //$template = preg_replace("/[;]*[\s]*\?\>[\n\r\t]*\<\? /s", ";", $template);
        //$template = preg_replace("/[;]+[\s]*\?\>[\n\r\t]*\<\?php[\s]*/s", ";", $template);
        //$template = preg_replace("/[\s]*\?\>[\n\r\t]*\<\?php[\s]*/s", "", $template);
        //echo code($template);

        return $template;
        //return strip_whitespace($template);
    }

    function looptags($var)
    {
        if (count($var) == 3) {
            return $this->stripvtags('<? if(is_array(' . $var[1] . ')) foreach(' . $var[1] . ' as ' . $var[2] . ') { ?>');
        } else {
            return $this->stripvtags('<? if(is_array(' . $var[1] . ')) foreach(' . $var[1] . ' as ' . $var[2] . '=>' . $var[3] . ') { ?>');
        }
    }

    function elseiftags($var)
    {
        return $this->stripvtags($var[1] . '<? } elseif(' . $var[2] . ') {?>' . $var[3]);
    }

    function iftags($var)
    {
        return $this->stripvtags($var[1] . '<? if(' . $var[2] . ') {?>' . $var[3]);
    }

    function echotags($var)
    {
        return $this->stripvtags('<? echo ' . $var[1] . '; ?>');
    }

    function templatetags($var)
    {
        $file = $var[1];
        $sub = $this->subtemplates;
        $this->subtemplates = array();
        $tplfile = $this->getTpl($file);
        $this->subtemplates = $sub;
        if ($content = @implode('', file($tplfile))) {
            $this->subtemplates[] = $file;
            return $content;
        } else {
            return '<!-- ' . $file . ' -->';
        }
    }

    function addquote($var)
    {
        $var = '<?=' . $var[1] . '?>';
        return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
    }

    function processvalue($v)
    {
        global $_G;
        return eval("return $_G$v");
    }

    function striptpl($var)
    {
        list(, $var, $s) = $var;
        $s = str_replace('\\"', '"', $s);
        $s = preg_replace("/<\?=\\\$(.+?)\?>/", "{\$\\1}", $s);
        preg_match_all("/<\?=(.+?)\?>/", $s, $constary);
        $constadd = '';
        $constary[1] = array_unique($constary[1]);
        $sc = $re = array();
        foreach ($constary[1] as $const) {
            $sc[] = $const;
            $re[] = md5($const);
            $constadd .= '$__' . md5($const) . ' = ' . $const . ';';
        }
        $s = preg_replace("/<\?=(.+?)\?>/e", "\$this->_tpl('\\1')", $s); //{\$__md5(\\1)}"
        $s = str_replace('?>', "\n\$$var .= <<<EOF\n", $s);
        $s = str_replace('<?', "\nEOF;\n", $s);
        $s = "<?php\n$constadd\$$var = <<<EOF\n" . $s . "\nEOF;\n?>";
        $s = preg_replace("/<<<EOF[\n\r\s\t]*EOF;/", "'';", $s);
        $s = str_replace("\$$var .= '';", '', $s);
        return $s;
    }

    function _tpl($var)
    {
        $var = trim($var);
        if (substr($var, 0, -1) == ';') {
            $var = substr($var, 0, -1);
        }
        return '{$__' . md5($var) . '}';
    }

    function customfunc($para)
    {
        list(, $func, $parameter) = $para;
        $parameter = stripslashes($parameter);
        $i = count($this->replacecode['search']);
        $this->replacecode['search'][$i] = $search = "<!--CUSTOM_FUNCTION_$i-->";
        $this->replacecode['replace'][$i] = "<?php echo $func($parameter); ?>";
        return $search;
    }

    function datetags($parameter)
    {
        $parameter = $parameter[1];
        $parameter = stripslashes($parameter);
        $i = count($this->replacecode['search']);
        $this->replacecode['search'][$i] = $search = "<!--DATE_TAG_$i-->";
        $this->replacecode['replace'][$i] = "<?php echo dgmdate($parameter, 'u'); ?>";
        return $search;
    }

    function avatartags($parameter)
    {
        $parameter = $parameter[1];
        $parameter = stripslashes($parameter);
        $i = count($this->replacecode['search']);
        $this->replacecode['search'][$i] = $search = "<!--AVATAR_TAG_$i-->";
        $this->replacecode['replace'][$i] = "<?php echo avatar($parameter); ?>";
        return $search;
    }

    function evaltags($php)
    {
        $php = $php[1];
        $php = str_replace('\"', '"', $php);
        $i = count($this->replacecode['search']);
        $this->replacecode['search'][$i] = $search = "<!--EVAL_TAG_$i-->";
        $this->replacecode['replace'][$i] = "<?php $php ?>";
        return $search;
    }

    function stripphpcode($type, $code)
    {
        $this->phpcode[$type][] = $code;
        return '{phpcode:' . $type . '/' . (count($this->phpcode[$type]) - 1) . '}';
    }

    function processCustom($var)
    {
        $type = 'script';
        if (preg_match("/\{CustomStyle\}/is", $var[0])) {
            $type = 'style';
        }
        return '<?php processCustom(\'' . $type . '\');?>';
    }

    function transamp($str)
    {
        $str = $str[0];
        $str = str_replace('&', '&amp;', $str);
        $str = str_replace('&amp;amp;', '&amp;', $str);
        $str = str_replace('\"', '"', $str);
        return $str;
    }

    function stripvtags($expr, $statement = '')
    {
        $expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
        $statement = str_replace("\\\"", "\"", $statement);
        return $expr . $statement;
    }

    function stripscriptamp($var)
    {
        list(, $s, $extra) = $var;
        $extra = str_replace('\\"', '"', $extra);
        $s = str_replace('&amp;', '&', $s);
        return "<script src=\"$s\" type=\"text/javascript\"$extra></script>";
    }

    function subtemplatetags($var)
    {
        return $this->stripvtags("<? include \$this->view->gettpl('" . $var[1] . "');?>");
    }

    /**
     * 将模板中的块替换成BLOCK函数
     *
     * @param string $cachekey ：
     * @param string $parameter ：
     * @return
     */
    function urltags($parameter)
    {
        $parameter = $parameter[1];
        if (strpos($parameter, '$_G')) {
            $parameter = preg_replace('/\$_G([^\/]+)/is', '".\$_G\\1."', $parameter);
        }
        $parameter = str_replace('\\"', '"', $parameter);
        return $this->stripvtags("<?php echo U($parameter);?>");
    }
}
