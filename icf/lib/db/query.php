<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/20
 * blog:blog.icodef.com
 * function:数据库查询
 *============================
 */

namespace icf\lib\db;

use PDO;

class query {
    private static $db = null;
    private static $db_type = '';

    public function __construct($table = '') {
        if (self::$db == null) {
            self::$db_type = input('config.db.type');
            $dns = call_user_func('icf\\lib\\db\\' . self::$db_type . '::dns');
            self::$db = new PDO($dns, input('config.db.user'), input('config.db.pwd'));
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        //处理表前缀
        $this->table = input('config.db.prefix') . str_replace('|', ',' . input('config.db.prefix'), $table);
    }

    private $table = '';
    private $where = '';
    private $field = '';
    private $order = '';
    private $limit = '';

    private $lastOper = 'and';
    private $bindParam = [];

    /**
     * 条件
     * @author Farmer
     * @param $field
     * @param string $value
     * @param string $operator
     * @return $this
     */
    public function where($field, $value = '', $operator = '=') {
        $this->where .= ' ' . (empty($this->where) ? '' : $this->lastOper . ' ');
        //恢复默认运算符
        $this->lastOper = 'and';
        if (is_array($field)) {
            //获取最后一个
            $keys = array_keys($field);
            foreach ($field as $key => $item) {
                $this->where .= "`$key`$operator? ";
                $this->bindParam[] = $item;
                if ($key != end($keys)) {
                    $this->where .= 'and ';
                }
            }
        } else if (is_string($field)) {
            if (empty($value)) {
                $this->where .= $field;
            } else {
                $this->where .= " `$field`$operator?";
                $this->bindParam[] = $value;
            }
        }
        return $this;
    }


    /**
     * 插入数据
     * @author Farmer
     * @param array $items
     * @return bool|int
     */
    public function insert(array $items) {
        if (!empty ($items)) {
            $param = [];
            $sql = 'insert into ' . $this->table . '(`' . implode('`,`', array_keys($items)) . '`) values(';
            foreach ($items as $value) {
                $sql .= '?,';
                $param[] = $value;
            }
            $sql = substr($sql, 0, strlen($sql) - 1);
            $sql .= ')';
            $result = self::$db->prepare($sql);
            if ($result->execute($param)) {
                return $result->rowCount();
            }
            return false;
        }
        return false;
    }

    /**
     * and
     * @author Farmer
     * @return $this
     */
    public function _and() {
        $this->lastOper = 'and';
        return $this;
    }

    /**
     * or
     * @author Farmer
     * @return $this
     */
    public function _or() {
        $this->lastOper = 'or';
        return $this;
    }

    /**
     * 查询记录
     * @author Farmer
     * @return bool|record
     */
    public function select() {
        $sql = 'select ' . ($this->field ?: '*') . " from {$this->table} " . ($this->where ? 'where' : '');
        $sql .= $this->dealParam();
        $result = self::$db->prepare($sql);
        if ($result->execute($this->bindParam)) {
            return new record($result);
        }
        return false;
    }

    public function count() {
        return $this->field('count(*)')->find()['count(*)'];
    }

    /**
     * 数据更新
     * @author Farmer
     * @param $set
     * @return bool|int
     */
    public function update($set) {
        $data = null;
        if (is_string($set)) {
            $data = $set;
        } else if (is_array($set)) {
            foreach ($set as $key => $value) {
                if (is_numeric($key)) {
                    $data .= ',' . $set[$key];
                } else {
                    $data .= ",`{$key}`=?";
                    $tmpParam[] = $value;
                }
            }
            $this->bindParam = array_merge($tmpParam, $this->bindParam);
            $data = substr($data, 1);
        }
        $sql = "update {$this->table} set $data where" . $this->dealParam();
        $result = self::$db->prepare($sql);
        if ($result->execute($this->bindParam)) {
            return $result->rowCount();
        }
        return false;
    }

    /**
     * 删除数据
     * @author Farmer
     * @return bool|int
     */
    public function delete() {
        $sql = "delete from {$this->table} where" . $this->dealParam();
        $result = self::$db->prepare($sql);
        if ($result->execute($this->bindParam)) {
            return $result->rowCount();
        }
        return false;
    }

    /**
     * 对where等进行处理
     * @author Farmer
     * @return string
     */
    private function dealParam() {
        $sql = $this->where ?: '';
        $sql .= $this->order ?: '';
        $sql .= $this->limit ?: '';
        return $sql;
    }

    /**
     * 绑定参数
     * @author Farmer
     * @param $key
     * @param string $value
     * @return $this
     */
    public function bind($key, $value = '') {
        if (is_array($key)) {
            $this->bindParam = array_merge($this->bindParam, $key);
        } else {
            $this->bindParam[$key] = $value;
        }
        return $this;
    }

    /**
     * 排序
     * @author Farmer
     * @param $field
     * @param string $rule
     * @return $this
     */
    public function order($field, $rule = 'desc') {
        $this->order = " order by `$field` $rule";
        return $this;
    }

    /**
     * 分页
     * @author Farmer
     * @param $start
     * @param int $count
     * @return $this
     */
    public function limit($start, $count = 0) {
        if ($count) {
            $this->limit = " limit $start,$count";
        } else {
            $this->limit = " limit $start";
        }
        return $this;
    }

    /**
     * 查询出单条数据
     * @author Farmer
     * @return mixed
     */
    public function find() {
        return $this->limit('1')->select()->fetch();
    }

    /**
     * 开始事务
     * @author Farmer
     */
    public function begin() {
        $this->exec('begin');
    }

    /**
     * 提交事务
     * @author Farmer
     */
    public function commit() {
        $this->exec('commit');
    }

    /**
     * 回滚事务
     * @author Farmer
     */
    public function rollback() {
        $this->exec('rollback');
    }

    public function field($field, $alias = '') {
        if (is_string($field)) {
            if (empty($alias)) {
                $this->field .= (empty($this->field) ? '' : ',') . $field . ' ';
            } else {
                $this->field .= (empty($this->field) ? '' : ',') . $field . ' as ' . $alias . ' ';
            }
        } else if (is_array($field)) {
            foreach ($field as $key => $value) {
                if (is_array($value)) {
                    $this->field .= (empty($this->field) ? '' : ',') . $key . ' as ' . $value . ' ';
                } else if (is_string($value)) {
                    $this->field .= (empty($this->field) ? '' : ',') . $value . ' ';
                }

            }
        }
        return $this;
    }

    public function __call($func, $arguments) {
        if (is_null(self::$db)) {
            return 0;
        }
        return call_user_func_array(array(
            self::$db,
            $func
        ), $arguments);
    }
}

/**
 * 记录集类
 * @author Farmer
 * @package icf\lib
 */
class record {
    private $result;

    public function __call($func, $arguments) {
        if (is_null($this->result)) {
            return 0;
        }
        return call_user_func_array(array(
            $this->result,
            $func
        ), $arguments);
    }

    function __construct(\PDOStatement $result) {
        $this->result = $result;
        $this->result->setFetchMode(PDO::FETCH_ASSOC);
    }

    function fetchAll() {
        return $this->result->fetchAll();
    }

    function fetch() {
        return $this->result->fetch();
    }

}