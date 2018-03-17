<?php

namespace TP3;

/**
 * 兼容3.2.3的模型，其实就是将5.0的Model与Query杂交了一下
 */
class Model extends \think\Model
{
    // 操作状态，暂时为了兼容3.2.3迁移过来的模型，先保留着等以后再用
    const MODEL_INSERT = 1; //  插入模型数据
    const MODEL_UPDATE = 2; //  更新模型数据
    const MODEL_BOTH = 3; //  包含上面两种方式
    const MUST_VALIDATE = 1; // 必须验证
    const EXISTS_VALIDATE = 0; // 表单存在字段则验证
    const VALUE_VALIDATE = 2; // 表单值不为空则验证

    /**
     * 调用完成后需要返回结果的方法
     *
     * @var mixed
     */
    var $_force_return = array(
        'select', 'find', 'count', 'column', 'value', 'setfield', 'delete', 'insert', 'update'
    );
    private $_return_array = true;          //返回值类型：true：数组，false：对象
    protected $_options = array();
    // 查询参数，其实主要是为了防止调用时却发现这玩意不存在（为了迎合think/db/Query）
    protected $options = [];

    /**
     * 架构函数
     * @access public
     * @param array|object $data 数据
     */
    public function __construct($data = '')
    {
        if (is_object($data)) {
            $this->data = get_object_vars($data);
        } else if (is_array($data)) {
            $this->data = $data;
        }
        if (is_string($data) && $data) {
            $this->name = $data;
        } else {
            $this->name = basename(str_replace('\\', '/', get_class($this)));
        }
        //TODO 这里得确认一下是否是__TABLE__格式的
        $this->_options['table'] = $this->parseTable($this->name);

        $this->initialize();
        $this->relation = new \think\model\Relation($this);
    }

    /**
     * 弥补一下thinkphp原生模型的一些不足的地方
     *
     * @param mixed $method
     * @param mixed $args
     * @return \think\Model
     */
    public function __call($method, $args = array())
    {
        $options = &$this->_options[$this->getTableName()];
        $options['table'] = $this->getTableName();
        $_return_array = $this->_return_array;
        try {
            parent::__call($method, $args);
        } catch (\Exception $e) {
            $method = strtolower($method);
            if ($method === 'setproperty') {
                return $this;
            }
            //thinkphp的原生模型里不支持的方法
            $model = self::db()->query;
            $result = NULL;
            if (method_exists($model, $method)) {
                $_dboptions = $model->getOptions();
                $_options = array_merge($_dboptions, $options);
                if ($_return_array) {
                    $_options['model'] = ''; //清空以后将以数组形式返回查询结果
                }
                $model->options($_options);
                //$this->setOptions($_options);
                $result = call_user_func_array([$model, $method], $args);
                $_dboptions = $model->getOptions();
                $this->setOptions($_dboptions);
                //$model->options($_dboptions);   //恢复DB的options
            }
            if (in_array($method, $this->_force_return) && !is_null($result)) {
                //查询结束，清除一下选项
                $this->_options = array(
                    'table' => $this->_options['table']
                );
                $model->setProperty('options', array());
                return $result;
            }
            //没有执行或者执行失败
            if (is_null($result)) {
                E($e->getMessage(), MY_QUERY_ERROR);
            } else {
                return $this;
            }
        }
    }

    /**
     * 初始化模型
     *
     * @return void
     */
    protected function initialize()
    {
        $trigger = array(
            'before_write', 'before_update', 'after_update', 'before_insert', 'after_insert', 'after_write', 'before_delete', 'after_delete'
        );
        foreach ($trigger as $event) {
            if (method_exists($this, $event)) {
                self::event($event, array(self, $event));
            }
        }
        parent::initialize();
    }

    /**
     * 获取系统中当前已经设置的选项
     * @return type
     */
    private function getDbOptions()
    {
        $model = self::db()->query;
        return $model->getOptions();
    }

    /**
     * 设置系统数据库查询的options
     * TODO: 理想的状态是这个options会跟着模型走，目前就只保存一个吧
     *
     * @param type $options
     * @return type
     */
    private function setDbOptions($options)
    {
        $model = self::db()->query;
        return $model->options($options);
    }

    /**
     * 获取全部options
     * @return type
     */
    public function getAllOptions()
    {
        return array_merge($this->getDbOptions(), $this->_options[$this->getTableName()]);
    }

    /**
     * 获取当前表名
     * @return string
     */
    public function getTableName()
    {
        return $this->_options['table'] ?: E('系统出错，尚未设置表名！');
    }

    /**
     * 设置返回值类型（true:返回数组，false:返回对象）
     *
     * @param boolean $type
     * @return Model
     */
    public function setResultArray($type = false)
    {
        $this->_return_array = $type;
        return $this;
    }

    /**
     * 指定数据表别名
     * @access public
     * @param string $alias 数据表别名
     * @return Model
     */
    public function alias($alias = '')
    {
        if ($alias && is_string($alias)) {
            $this->_options[$this->getTableName()]['alias'] = $alias;
        }
        return $this;
    }

    /**
     * 指定排序
     * @access public
     * @param string $orderby 排序字段
     * @param string $order 排序
     * @return Model
     */
    public function order($orderby = '', $order = '')
    {
        if ($orderby) {
            if ($order) {
                $this->_options[$this->getTableName()]['order'][] = $orderby . ' ' . $order;
            } else {
                $this->_options[$this->getTableName()]['order'][] = $orderby;
            }
        }
        return $this;
    }

    /**
     * 指定查询数量
     * @access public
     * @param int $start 起始位置
     * @param int $offset 查询数量
     * @return Model
     */
    public function limit($start, $offset = 0)
    {
        if (is_array($start)) {
            list($start, $offset) = $start;
            $this->_options[$this->getTableName()]['limit'] = $start . ',' . $offset;
        } else if ($offset) {
            $this->_options[$this->getTableName()]['limit'] = $start . ',' . $offset;
        } else {
            $this->_options[$this->getTableName()]['limit'] = $start;
        }
        return $this;
    }

    /**
     * 指定分页
     * @access public
     * @param mixed $start 页数
     * @param mixed $offset 每页数量
     * @return {Model|Model}
     */
    public function page($start, $offset)
    {
        $start = (max($start, 1) - 1) * $offset;
        return $this->limit($start, $offset);
    }

    public function setOptions($options)
    {
        $this->_options[$this->getTableName()] = $options;
        //$this->setDbOptions($options);
    }

    public function field($field = true)
    {
        if ($field === 'true') {
            $field = '*';
        }
        $field = (is_string($field) && trim($field)) ? $field : '*';
        return $this;
    }

    /**
     * SAVE的别名，主要是为了兼容3.2.3的模型操作
     *
     * @param array $data
     * @return Model
     */
    public function add($data = array())
    {
        return $this->save($data);
    }

    /**
     * 获取最近一次查询的sql语句
     * @access public
     * @return string
     */
    public function getLastSql()
    {
        return self::db()->getLastSql();
    }

    /**
     * 获取最近插入的ID
     * @access public
     * @return string
     */
    public function getLastInsID()
    {
        return self::db()->getLastInsID();
    }

    /**
     * 获取字段列表
     * @return type
     */
    public function getDbFields()
    {
        return self::db()->getTableInfo($this->_options['table'], 'fields');
    }

    /**
     * 设置要操作的表
     * @param type $table
     */
    public function table($table = '')
    {
        if ($table) {
            $this->_options['table'] = $this->parseTable($table);
        }
        return $this;
    }

    /**
     * 将指定的表名替换成带前缀的表名（小写）
     * @access public
     * @param string $table 表名
     * @return string
     */
    public function parseTable($table = '')
    {
        $prefix = self::db()->getConfig('prefix');
        if (false !== strpos($table, '__')) {
            $table = preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function ($match) use ($prefix) {
                return $prefix . strtolower($match[1]);
            }, $table);
        } else {
            $table = $prefix . \think\Loader::parseName($table);
        }
        return $table;
    }

    /**
     * 删除当前的记录
     * @access public
     * @return integer
     */
    public function delete()
    {
        if (false === $this->trigger('before_delete', $this)) {
            return false;
        }
        if ($this->data) {
            $result = self::db()->delete($this->data);
        } else {
            $options = $this->_options[$this->getTableName()];
            if (isset($options['where'])) {
                $model = self::db()->query;
                $options['model'] = ''; //清空以后将以数组形式返回查询结果
                $model->options($options);
                $result = $model->delete();
            } else {
                $this->error = "请指定要删除的记录！";
                return false;
            }
        }

        $this->trigger('after_delete', $this);
        return $result;
    }

}
