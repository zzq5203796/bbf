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
            return $this->fail($substr);
        }
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
}