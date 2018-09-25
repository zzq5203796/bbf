<?php

namespace libs;

use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/8/13
 * Time: 11:42
 */
class CPdo
{
    protected $_dsn = "mysql:host=localhost;dbname=test";
    protected $_name = "root";
    protected $_pass = "123456";
    protected $_condition = [];
    protected $pdo;
    protected $fetchAll;
    protected $query;
    protected $result;
    protected $num;
    protected $mode;
    protected $prepare;
    protected $row;
    protected $fetchAction;
    protected $beginTransaction;
    protected $rollback;
    protected $commit;
    protected $char;
    protected $error;
    protected $columns = [];
    protected $sqlLog = [];
    private
    static $get_mode;
    private
    static $get_fetch_action;

    /**
     *pdo construct
     * @param bool $pconnect
     */
    public function __construct($pconnect = false) {
        $this->_condition = [\PDO::ATTR_PERSISTENT => $pconnect];
        $this->pdo_connect();
    }

    /**
     *pdo connect
     */
    private function pdo_connect() {
        try {
            $this->pdo = new \PDO($this->_dsn, $this->_name, $this->_pass, $this->_condition);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            $this->setExceptionError($e->getMessage(), $e->getline, $e->getFile);
        }
    }

    /**
     *self sql get value action
     * @param $sql
     * @param string $fetchAction
     * @param null $mode
     * @return mixed
     */
    public function getValueBySelfCreateSql($sql, $fetchAction = "assoc", $mode = null) {
        $this->fetchAction = $this->fetchAction($fetchAction);
        $this->result = $this->setAttribute($sql, $this->fetchAction, $mode);
        $this->AllValue = $this->result->fetchAll();
        return $this->AllValue;
    }

    /**
     *select condition can query
     * @param $sql
     * @param $fetchAction
     * @param $mode
     * @return mixed
     */
    private function setAttribute($sql, $fetchAction, $mode) {
        $this->mode = self::getMode($mode);
        $this->fetchAction = self::fetchAction($fetchAction);
        $this->pdo->setAttribute(\PDO::ATTR_CASE, $this->mode);
        $this->query = $this->base_query($sql);
        $this->query->setFetchMode($this->fetchAction);
        return $this->query;
    }

    /**
     *get mode action
     * @param $get_style
     * @return int
     */
    private static function getMode($get_style) {
        switch ($get_style) {
            case null:
                self::$get_mode = \PDO::CASE_NATURAL;
                break;
            case true:
                self::$get_mode = \PDO::CASE_UPPER;
                break;
            case false;
                self::$get_mode = \PDO::CASE_LOWER;
                break;
        }
        return self::$get_mode;
    }

    /**
     *fetch value action
     * @param $fetchAction
     * @return int
     */
    private static function fetchAction($fetchAction) {
        switch ($fetchAction) {
            case "assoc":
                self::$get_fetch_action = \PDO::FETCH_ASSOC; //asso array
                break;
            case "num":
                self::$get_fetch_action = \PDO::FETCH_NUM; //num array
                break;
            case "object":
                self::$get_fetch_action = \PDO::FETCH_OBJ; //object array
                break;
            case "both":
                self::$get_fetch_action = \PDO::FETCH_BOTH; //assoc array and num array
                break;
            default:
                self::$get_fetch_action = \PDO::FETCH_ASSOC;
                break;
        }
        return self::$get_fetch_action;
    }

    /**
     * get total num action
     * @param $sql
     * @return mixed
     */
    public function rowCount($sql) {
        $this->result = $this->base_query($sql);
        $this->num = $this->result->rowCount();
        return $this->num;
    }

    /*
    *simple query and easy query action
    */
    public function query($table, $column = "*", $condition = [], $group = "", $order = "", $having = "", $startSet = "", $endSet = "", $fetchAction = "assoc", $params = null) {
        $sql = "select " . $column . " from `" . $table . "` ";
        $where = "";
        if ($condition != null) {
            foreach ($condition as $key => $value) {
                $where .= "$key = '$value' and ";
            }
            $sql .= "where $where";
            $sql .= "1 = 1 ";
        }
        if ($group != "") {
            $sql .= "group by " . $group . " ";
        }
        if ($order != "") {
            $sql .= " order by " . $order . " ";
        }
        if ($having != "") {
            $sql .= "having '$having' ";
        }

        if ($startSet !== "" && $endSet !== "" && is_numeric($endSet) && is_numeric($startSet)) {
            $sql .= "limit $startSet,$endSet";
        }
        $this->result = $this->getValueBySelfCreateSql($sql, $fetchAction, $params);
        return $this->result;
    }

    /**
     * execute delete update insert and so on action
     * @param $sql
     * @return string
     */
    public function exec($sql) {
        $this->setChars();
        $this->result = $this->pdo->exec($sql);
        $substr = substr($sql, 0, 6);
        if ($this->result) {
            return $this->successful($substr);
        } else {
            $error = $this->pdo->errorInfo();
            $error['sql'] = $sql;
            $error['sub'] = $substr;
            $this->error = $error;
            return $this->fail($substr);
        }
    }

    public function errorInfo() {
        return $this->error;
    }

    /**
     * prepare action
     * @param $sql
     * @return mixed
     */
    public function prepare($sql) {
        $this->prepare = $this->pdo->prepare($sql);
        $this->setChars();
        $this->prepare->execute();
        while ($this->rowz = $this->prepare->fetch()) {
            return $this->row;
        }
    }

    /**
     *USE transaction
     * @param $sql
     */
    public function transaction($sql) {
        $this->begin();
        $this->result = $this->pdo->exec($sql);
        if ($this->result) {
            $this->commit();
        } else {
            $this->rollback();
        }
    }

    /**
     *start transaction
     */
    private function begin() {
        $this->beginTransaction = $this->pdo->beginTransaction();
        return $this->beginTransaction;
    }

    /**
     *commit transaction
     */
    private function commit() {
        $this->commit = $this->pdo->commit();
        return $this->commit;
    }

    /**
     *rollback transaction
     */
    private function rollback() {
        $this->rollback = $this->pdo->rollback();
        return $this->rollback;
    }

    /**
     * base query
     * @param $sql
     * @return mixed
     */
    private function base_query($sql) {
        $this->setChars();
        $this->query = $this->pdo->query($sql);
        return $this->query;
    }

    /**
     *set chars
     */
    private function setChars() {
        $this->char = $this->pdo->query("SET NAMES 'UTF8'");
        return $this->char;
    }

    /**
     *process sucessful action
     * @param $params
     * @return string
     */
    private function successful($params) {
        return true;
    }

    /**
     *process fail action
     * @param $params
     * @return string
     */
    private function fail($params) {
        return false;
    }

    /**
     *process exception action
     * @param $getMessage
     * @param $getLine
     * @param $getFile
     * @return string
     */
    private function setExceptionError($getMessage, $getLine, $getFile) {
        throw new \Exception("Error message is " . $getMessage . "<br /> The Error in " . $getLine . " line <br /> This file dir on " . $getFile);
    }

    public function getColumns($table) {
        if (empty($this->columns[$table])) {
            $sql = "SHOW COLUMNS FROM `$table`";
            $this->columns[$table] = $this->getValueBySelfCreateSql($sql);
        }
        return $this->columns[$table];
    }

    public function getSqlLog() {
        return $this->sqlLog;
    }

    public function checkData($table, $data) {
        $columns = $this->getColumns($table);
        $res = [];
        foreach ($columns as $vo) {
            $field = $vo['Field'];
            if (isset($data[$field])) {
                $res[$field] = $data[$field];
            }
        }
        return $res;
    }

    /**
     * 获取主键
     * todo 先忽略组合键
     * @param $columns
     * @return bool
     */
    public function getPri($table) {
        $columns = $this->getColumns($table);
        $res = false;
        foreach ($columns as $vo) {
            if ($vo['Key'] == 'PRI') {
                $res = $vo['Field'];
                break;
            }
        }
        return $res;
    }

    public function add($table, $data, $map = []) {
        $data = $this->checkData($table, $data);
        $pri = $this->getPri($table);
        if ($pri && empty($data[$pri]))
            unset($data[$pri]);

        $sql = "INSERT INTO ";
        list($setSql, $mapData) = $this->FDFields($data);
        $sql .= $table . ' set ' . $setSql;
        try{
            $pdoStatement = $this->pdo->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $execRet = $pdoStatement->execute($mapData);
            $this->sqlLog[] = [$sql, $mapData];
            return $execRet? $this->pdo->lastInsertId(): false;
        }catch (\PDOException $e){
            dump($e->getCode());
            dump($e->getMessage());
            return false;
        }
    }

    public function update($table, $data, $map = []) {
        $data = $this->checkData($table, $data);
        $map = $this->checkData($table, $map);
        $pri = $this->getPri($table);
        if ($pri) {
            if (isset($data[$pri]) && $data[$pri] > 0) {
                $map[$pri] = $data[$pri];
            }
            unset($data[$pri]);
        }

        $sql = 'UPDATE `' . $table . '` SET ';
        list($setSql, $mapSetData) = $this->FDFields($data);
        list($mapSql, $mapData) = $this->FDFields($map, 'and');

        $sql .= $setSql;
        $mapData = array_merge($mapData, $mapSetData);
        //        list($where, $mapData) = $this->FDCondition($mapData, $mapData); // todo
        //        $sql .= $where ? ' WHERE ' . $where : '';

        $sql .= $mapSql? ' WHERE ' . $mapSql: '';

        $pdoStatement = $this->pdo->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $execRet = $pdoStatement->execute($mapData);
        $this->sqlLog[] = [$sql, $mapData];
        return $execRet? $pdoStatement->rowCount(): false;
    }

    public function save($table, $data, $map = []) {
        $data = $this->checkData($table, $data);
        $pri = $this->getPri($table);
        $is_add = true;
        if (!empty($map) || ($pri && isset($data[$pri]) && $data[$pri] > 0)) {
            $is_add = false;
        }

        if ($is_add) {
            $res = $this->add($table, $data);
        } else {
            $res = $this->update($table, $data, $map);
        }
        return $res;
    }

    public function delete($table, $map) {
        if (!empty($table)) {
            $map = $this->checkData($table, $map);
            $sql = 'DELETE FROM ' . $table;
            list($mapSql, $mapData) = $this->FDFields($map, 'and');
            $sql .= $mapSql? ' WHERE ' . $mapSql: '';
            $pdoStatement = $this->pdo->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $execRet = $pdoStatement->execute($mapData);
            $this->sqlLog[] = [$sql, $mapData];
            return $execRet;
        }
        return false;
    }

    public function fetch($sql, $searchData = [], $dataMode = \PDO::FETCH_ASSOC, $preType = [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]) {
        if ($sql) {
            $sql .= ' limit 1';
            $pdoStatement = $this->pdo->prepare($sql, $preType);
            $pdoStatement->execute($searchData);
            $this->sqlLog[] = [$sql, $searchData];
            return $data = $pdoStatement->fetch($dataMode);
        } else {
            return false;
        }
    }

    public function fetchAll($sql, $searchData = [], $limit = [0, 10], $dataMode = \PDO::FETCH_ASSOC, $preType = [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]) {
        if ($sql) {
            $sql .= ' limit ' . (int)$limit[0] . ',' . (intval($limit[1]) > 0? intval($limit[1]): 10);
            $pdoStatement = $this->pdo->prepare($sql, $preType);
            $pdoStatement->execute($searchData);
            return $data = $pdoStatement->fetchAll($dataMode);
            $this->sqlLog[] = [$sql, $searchData];
        } else {
            return false;
        }
    }

    public function FDFields($data, $link = ',', $judge = [], $aliasTable = '') {
        $sql = '';
        $mapData = [];
        foreach ($data as $key => $value) {
            $mapIndex = ':' . ($link != ','? 'c': '') . $aliasTable . $key;
            $sql .= ' ' . ($aliasTable? $aliasTable . '.': '') . '`' . $key . '` ' . ($judge[$key]? $judge[$key]: '=') . ' ' . $mapIndex . ' ' . $link;
            $mapData[$mapIndex] = $value;
        }
        $sql = trim($sql, $link);
        return [$sql, $mapData];
    }

    //用于处理单个字段处理
    public function FDField($field, $value, $judge = '=', $preMap = 'cn', $aliasTable = '') {
        $mapIndex = ':' . $preMap . $aliasTable . $field;
        $sql = ' ' . ($aliasTable? $aliasTable . '.': '') . '`' . $field . '`' . $judge . $mapIndex;
        $mapData[$mapIndex] = $value;
        return [$sql, $mapData];
    }

    //使用刚方法可以便捷产生查询条件及对应数据数组
    public function FDCondition($condition, $mapData) {
        if (is_string($condition)) {
            $where = $condition;
        } elseif (is_array($condition)) {
            if ($condition['str']) {
                if (is_string($condition['str'])) {
                    $where = $condition['str'];
                } else {
                    return false;
                }
            }
            if (is_array($condition['data'])) {
                $link = $condition['link']? $condition['link']: 'and';
                list($conSql, $mapConData) = $this->FDFields($condition['data'], $link, $condition['judge']);
                if ($conSql) {
                    $where .= ($where? ' ' . $link: '') . $conSql;
                    $mapData = array_merge($mapData, $mapConData);
                }
            }
        }
        return [$where, $mapData];
    }

    public function getLastSql() {
        if (empty($this->sqlLog)) {
            return '';
        }
        list($sql, $map) = $this->sqlLog[count($this->sqlLog) - 1];
        $old = $rep = [];
        krsort($map);
        foreach ($map as $key => $vo) {
            $old[] = $key;
            $rep[] = $vo;
        }
        $sql = str_replace($old, $rep, $sql);
        return $sql;
    }
}