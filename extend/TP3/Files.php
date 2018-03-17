<?php

namespace TP3;

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/**
 * 文件系统实用函数集
 * @package 简易CMS
 * @subpackage Files
 */
class Files
{

    /**
     * 自动创建文件夹
     *
     * @param string 要创建的文件夹
     * @param int 文件夹权限
     * @return boolean 如果成功则返回真
     */
    static function autocreatePath($path = '', $mode = '0777')
    {
        $path = Files::formatpath($path, false);
        Files::check($path);
        $path = Files::getNativePath($path, false, true);

        // 检查此文件夹是否存在
        if (file_exists($path)) {
            return true;
        }

        // 设置权限
        $origmask = @umask(0);
        $mode = octdec($mode);

        $parts = explode(DS, $path);
        $n = count($parts);
        $ret = true;
        if ($n < 1) {
            $ret = false;
        } else {
            $path = $parts[0];
            for ($i = 1; $i < $n; $i++) {
                $path .= DS . $parts[$i];
                //需要跳过根目录
                if (strlen($path) < strlen(ROOT_PATH)) {

                } elseif (!file_exists($path)) {
                    if (!Files::mkdir($path, $mode)) {
                        $ret = false;
                        break;
                    }
                }
            }
        }
        @umask($origmask);

        return $ret;
    }

    /**
     * Gets the extension of a file name
     * @param string The file name
     * @return string
     */
    static function getExt($file)
    {
        $dot = strrpos($file, '.') + 1;
        return substr($file, $dot);
    }

    /**
     * 清除指定文件的扩展名
     * @param string $file 文件名
     * @return string 主文件名
     */
    static function stripExt($file)
    {
        return preg_replace('#\.[^.]*$#', '', $file);
    }

    /**
     * 检测是否有权修改文件属性的权限
     * @param string $file 文件路径
     * @return boolean
     */
    static function canCHMOD($file)
    {
        $perms = fileperms($file);
        if ($perms !== false)
            if (@chmod($file, $perms ^ 0001)) {
                @chmod($file, $perms);
                return true;
            }
        return false;
    }

    /**
     * Checks for snooping outside of the file system root
     * @param string A file system path to check
     */
    static function check($path)
    {
        global $control, $_errorjump;
        //if(substr($path, 0, 4)=='http') return true;
        $path = Files::formatpath($path, false);
        $_errorjump = 2;
        if (strpos($path, '..') !== false) {
            trigger_error(sprintf('Files::check 不允许使用相对路径<strong>%s</strong>！', $path));
            exit;
        }
        if (strpos(Files::getNativePath($path), Files::getNativePath(ROOT_PATH)) !== 0) {
            trigger_error(sprintf('指定的文件<strong>%s</strong>在网站根目录以外！' . ROOT_PATH, $path));
            exit;
        }
        return $path;
    }

    static function traceback($ignorefile = '')
    {
        if (function_exists('debug_backtrace')) {
            foreach (debug_backtrace() as $back) {
                if (@$back['file']) {
                    if ($ignorefile) {
                        if (basename($back['file']) != $ignorefile) {
                            $result[] = str_replace(ROOT_PATH, '', Files::formatpath($back['file'])) . ':' . $back['line'];
                        }
                    } else {
                        $result[] = str_replace(ROOT_PATH, '', Files::formatpath($back['file'])) . ':' . $back['line'];
                    }
                }
            }
        }
        return $result;
    }

    function _chmod($path, $mode)
    {
        global $control;
        return @chmod($path, $mode);
    }

    /**
     * Chmods files and directories recursivel to given permissions
     * @param path The starting file or directory (no trailing slash)
     * @param filemode Integer value to chmod files. NULL = dont chmod files.
     * @param dirmode Integer value to chmod directories. NULL = dont chmod directories.
     * @return TRUE=all succeeded FALSE=one or more chmods failed
     */
    static function CHMOD($path, $filemode = Files_FILEPEMS, $dirmode = Files_DIRPEMS)
    {
        Files::check($path);
        $ret = TRUE;
        if (is_dir($path)) {
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    $fullpath = $path . DS . $file;
                    if (is_dir($fullpath)) {
                        if (!Files::CHMOD($fullpath, $filemode, $dirmode)) {
                            $ret = FALSE;
                        }
                    } else {
                        if (isset($filemode)) {
                            if (!Files::_chmod($fullpath, $filemode)) {
                                $ret = FALSE;
                            }
                        }
                    } // if
                } // if
            } // while
            closedir($dh);
            if (isset($dirmode))
                if (!Files::_chmod($path, $dirmode)) {
                    $ret = FALSE;
                }
        } else {
            if (isset($filemode))
                $ret = Files::_chmod($path, $filemode);
        } // if
        return $ret;
    }

    /**
     * Copies a file
     * @param string The path to the source file
     * @param string The path to the destination file
     * @param string An optional base path to prefix to the file names
     * @return mixed
     */
    static function copy($src, $dest, $path = '')
    {
        if ($path) {
            $src = Files::getNativePath($path . $src, false);
            $dest = Files::getNativePath($path . $dest, false);
        }

        Files::check($src);
        Files::check($dest);

        if (!@file_exists($src)) {
            return 'Cannot find source file';
        }
        if (!is_writable($dest)) {
            if (!is_writable(dirname($dest))) {
                return 'Directory unwritable';
            }
        }
        if (!@copy($src, $dest)) {
            return 'Copy failed';
        }
        return true;
    }

    static function unlink($file)
    {
        return Files::deleteFile($file);
    }

    /**
     * 删除指定的文件
     * @param mixed 文件名或文件名列表
     * @return boolean  True on success
     */
    static function deleteFile($file)
    {
        if (is_array($file)) {
            $files = $file;
        } else {
            $files[] = $file;
        }
        $failed = 0;
        foreach ($files as $file) {
            $file = Files::getNativePath($file, false);
            Files::check($file);
            if (file_exists($file))
                $failed |= !@unlink($file);
        }
        return !$failed;
    }

    /**
     * 删除文件夹以及其中所包含的文件及文件夹
     * @param string $path 要删除的文件夹
     * @param boolean $rmdir 是否需要删除自身
     */
    static function deleteFolder($path)
    {
        $path = Files::getNativePath($path, false);
        Files::check($path);

        // remove files in folder
        $files = Files::listFiles($path, '.', false, true);
        if ($files)
            foreach ($files as $file) {
                if (strtolower(substr(basename($file), 0, 9)) == 'index.htm') {
                    if ($rmdir) Files::deleteFile($file);
                } else {
                    Files::deleteFile($file);
                }
            }

        // remove sub-folders
        $folders = Files::listFolders($path, '.', false, true);
        if ($folders)
            foreach ($folders as $folder) {
                Files::deleteFolder($folder);
            }
        $ret = true;
        // remove the folders
        if ($rmdir)
            $ret = rmdir($path);
        return $ret;
    }

    /** Wrapper for the standard file_exists function
     * @param string filename relative to installation dir
     * @return boolean
     */
    static function file_exists($file)
    {
        $file = Files::getNativePath($file, false);
        return @file_exists($file);
    }

    /**
     * Function to strip additional / or \ in a path name
     * @param string The path
     * @param boolean Add trailing slash
     */
    static function getNativePath($p_path, $p_addtrailingslash = true)
    {
        $retval = '';
        $path = trim($p_path);

        if (empty($p_path)) {
            $retval = ROOT_PATH;
        } else {
            $retval = Files::formatpath($path, false);
            $pos = strpos('##' . $retval, ROOT_PATH);
            if (!$pos) {
                $retval = ROOT_PATH . DS . $retval;
            } elseif ($pos != 2) {
                return '';
            }
            $retval = Files::formatpath($path, false);
        }
        if ($p_addtrailingslash) {
            if (substr($retval, -1) != DS) {
                $retval .= DS;
            }
        }
        return $retval;
    }

    static function formatPath($path, $cutroot = true)
    {
        $prefix = DS;
        $path = str_replace('\\.\\', DS, $path);
        $path = str_replace('/./', DS, $path);
        $path = str_replace('\\\\', DS, $path);
        $path = str_replace('//', DS, $path);
        $path = str_replace('\\', DS, $path);
        $path = str_replace('/', DS, $path);

        if (substr($path, 0, 2) == '.' . DS) $path = ROOT_PATH . substr($path, 1);
        $path = str_replace('\\.\\', DS, $path);
        $path = str_replace('/./', DS, $path);
        $path = str_replace('\\\\', DS, $path);
        $path = str_replace('//', DS, $path);

        if ($cutroot)
            $path = str_replace(ROOT_PATH, $prefix, $path);
        return $path;
    }

    /**
     * 获取文件属性
     *
     * @param 路径 $path
     * @return 属性
     */
    static function getPermissions($path)
    {
        $path = Files::getNativePath($path);
        if (!@file_exists($path)) $path = substr($path, 0, -1);
        if (!@file_exists($path)) return "---------";
        Files::check($path);
        $mode = @decoct(@fileperms($path) & 0777);

        if (strlen($mode) < 3) {
            return '---------';
        }
        $parsed_mode = '';
        for ($i = 0; $i < 3; $i++) {
            // read
            $parsed_mode .= ($mode{$i} & 04) ? "r" : "-";
            // write
            $parsed_mode .= ($mode{$i} & 02) ? "w" : "-";
            // execute
            $parsed_mode .= ($mode{$i} & 01) ? "x" : "-";
        }
        return $parsed_mode;
    }

    /**
     * 读取指定文件夹中的文件列表
     * @param 字符串 系统路径
     * @param 字符串 过滤条件
     * @param boolean 是否搜索子目录
     * @param boolean 是否包含完整路径
     * @return 数组
     */
    static function listFiles($path, $filter = '.', $recurse = false, $fullpath = false)
    {
        $arr = array();
        $path = Files::getNativePath($path, false);
        if (!is_dir($path)) {
            return $arr;
        }

        // prevent snooping of the file system
        Files::check($path);

        // read the source directory
        $handle = opendir($path);
        $path .= DS;
        while (($file = readdir($handle)) !== false) {
            $dir = $path . $file;
            $isDir = is_dir($dir);
            if ($file <> '.' && $file <> '..') {
                if ($isDir) {
                    if ($recurse) {
                        $arr2 = Files::listFiles($dir, $filter, $recurse, $fullpath);
                        $arr = array_merge($arr, $arr2);
                    }
                } else {
                    if (preg_match("/$filter/", $file)) {
                        if ($fullpath) {
                            $arr[] = $path . $file;
                        } else {
                            $arr[$file] = $file;
                        }
                    }
                }
            }
        }
        closedir($handle);
        asort($arr);
        return $arr;
    }

    /**
     * Utility function to read the folders in a directory
     * @param string The file system path
     * @param string A filter for the names
     * @param boolean Recurse search into sub-directories
     * @param boolean True if to prepend the full path to the file name
     * @return array
     */
    static function listFolders($path, $filter = '.', $recurse = false, $fullpath = false)
    {
        $arr = array();
        $path = Files::getNativePath($path, false);
        if (!is_dir($path)) {
            return $arr;
        }

        // prevent snooping of the file system
        Files::check($path);

        // read the source directory
        $handle = opendir($path);
        $path .= DS;

        while (($file = readdir($handle)) !== false) {
            $dir = $path . $file;
            $isDir = is_dir($dir);
            if (($file <> '.') && ($file <> '..') && $isDir) {
                // removes CVS directores from list
                if (preg_match("/$filter/", $file) && !(preg_match("/CVS/", $file) || preg_match("/SVN/", $file))) {
                    if ($fullpath) {
                        $arr[] = $dir;
                    } else {
                        $arr[$file] = $file;
                    }
                }
                if ($recurse) {
                    $arr2 = Files::listFolders($dir, $filter, $recurse, $fullpath);
                    $arr = array_merge($arr, $arr2);
                }
            }
        }
        closedir($handle);
        asort($arr);
        return $arr;
    }

    /**
     * Lists folder in format suitable for tree display
     */
    static function listFolderTree($path, $filter, $maxLevel = 3, $level = 0, $parent = 0)
    {
        $dirs = array();
        if ($level == 0) {
            $GLOBALS['_Files_folder_tree_index'] = 0;
        }

        if ($level < $maxLevel) {
            Files::check($path);
            $folders = Files::listFolders($path, $filter);
            // first path, index foldernames
            for ($i = 0, $n = count($folders); $i < $n; $i++) {
                $name = $folders[$i];
                if ($name != '.svn' && $name != '.cvs') {
                    $id = ++$GLOBALS['_Files_folder_tree_index'];
                    $fullName = Files::getNativePath($path . DS . $name, false);
                    $dirs[] = array(
                        'id' => $id,
                        'parent' => $parent,
                        'name' => $name,
                        'fullname' => $fullName,
                        'relname' => str_replace(ROOT_PATH, '', $fullName)
                    );
                    $dirs2 = Files::listFolderTree($fullName, $filter, $maxLevel, $level + 1, $id);
                    $dirs = array_merge($dirs, $dirs2);
                }
            }
        }

        return $dirs;
    }

    /**
     * Makes file name safe to use
     * @param string The name of the file (not full path)
     * @return string The sanitised string
     */
    function makeSafe($file)
    {
        $regex = '#\.\.|[^A-Za-z0-9\.\_\- ]#';
        return preg_replace($regex, '', $file);
    }

    /**
     * @param string The full file path
     * @param string The buffer to read into
     * @return boolean True on success
     */
    static function read($file, &$buffer)
    {
        Files::check($file);
        $file = Files::formatpath($file, false);
        if (@file_exists($file)) {
            $fsize = filesize($file);
            if (!$fsize) return false;
            $buffer = file_get_contents($file);
            if ($buffer) {
                return true;
            } else {
                if ($handle = @fopen($file, 'r')) {
                    $buffer = fread($handle, $fsize);
                    fclose($handle);
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * 将指定数据写入指定的文件中
     *
     * @param mixed $file
     * @param mixed $buffer
     * @param mixed $filemod
     * @param mixed $openmod
     * @param mixed $eixt
     */
    static function write($file, $buffer, $filemod = 'text', $openmod = 'w', $eixt = 0)
    {
        global $control, $_errorjump;
        Files::autocreatePath(dirname($file));
        Files::check($file);
        $_errorjump = 1;
        if (!is_writable($file)) {
            if (!is_writable(dirname($file))) {
                if ($eixt) {
                    trigger_error(sprintf('对不起，您指定的文件 %s 已被写保护!', $file));
                }
                return false;
            }
        }
        if (!@$fp = fopen($file, $openmod)) {
            if ($eixt) {
                trigger_error(sprintf('对不起，您指定的文件 %s 已被写保护！', $file));
            } else {
                return false;
            }
        } else {
            $text = '';
            if ($filemod == 'php') {
                $text = "<?php\r\n\r\ndefined('IN_JYCMS') or die('Direct Access to this location is not allowed.');\r\n\r\n";
            }
            $text .= $buffer;
            if ($filemod == 'php') {
                $text .= "\r\n\r\n?>";
            }
            @flock($fp, 2);
            fwrite($fp, $text);
            fclose($fp);
            return true;
        }
    }

    /**
     * @param string The name of the php (temporary) uploaded file
     * @param string The name of the file to put in the temp directory
     * @param string The message to return
     */
    static function uploadfile($srcFile, $destFile, &$msg)
    {
        $srcFile = Files::getnativepath($srcFile, false);
        $destFile = Files::getnativepath($destFile, false);
        Files::check($destFile);
        $baseDir = dirname($destFile);
        if (@file_exists($baseDir)) {
            if (is_writable($baseDir)) {
                if (move_uploaded_file($srcFile, $destFile)) {
                    if (Files::chmod($destFile)) {
                        return true;
                    } else {
                        $msg = Files_ERR01;
                    }
                } else if (!($handle = fopen($srcFile, "r"))) {
                    $msg = Files_ERR02;
                } else {
                    $contents = fread($handle, filesize($srcFile));
                    fclose($handle);
                    if (!($handle = fopen($destFile, "a"))) {
                        $msg = "Cann't create destfile";
                    } else if (fwrite($handle, $contents) === FALSE) {
                        fclose($handle);
                        $msg = "Cann't create destfile";
                        return false;
                    } else {
                        fclose($handle);
                        return true;
                    }
                }
            } else {
                $msg = Files_ERR03;
            }
        } else {
            $msg = Files_ERR04;
        }
        return false;
    }

    /**
     * Blank index file
     */
    static function htmlIndex()
    {
        return '<html><body></body></html>';
    }

    /**
     * 创建文件夹并自动生成空的索引文件
     *
     * @param 路径 $path
     * @param 属性 $mode
     * @return 是否创建成功
     */
    static function mkdir($path, $mode = '0777')
    {
        global $control;
        if (substr($path, 0, 2) == './') $path = ROOT_PATH . substr($path, 1);
        $path = Files::formatpath($path, false);
        if (@file_exists($path)) return true;
        if (!@is_dir($path)) {
            if (!@mkdir($path, 0777)) {
                $ret = false;
            } else {
                $file = $path . DS . 'index.html';
                Files::write($file, Files::htmlIndex());
                $ret = true;
            }
        } else {
            $ret = true;
        }
        return $ret;
    }

    /**
     * 格式化大小函数,根据字节数自动显示成'KB','MB'等等
     *
     * @param 尺寸 $size
     * @param unknown_type $prec
     * @return 格式化后的尺寸
     */
    static function formatsize($size, $prec = 3)
    {
        $size = round(abs($size));
        $units = array(0 => " B ", 1 => " KB", 2 => " MB", 3 => " GB", 4 => " TB");
        if ($size == 0)
            return str_repeat(" ", $prec) . '0' . $units[0];
        $unit = min(4, floor(log($size) / log(2) / 10));
        $size = $size * pow(2, -10 * $unit);
        $digi = $prec - 1 - floor(log($size) / log(10));
        $size = round($size * pow(10, $digi)) * pow(10, -$digi);
        return $size . $units[$unit];
    }
}

?>