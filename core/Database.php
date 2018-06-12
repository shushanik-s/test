<?php

const DBConfig = [
    'name' => 'test',
    'user' => 'root',
    'pass' => "",
    'host' => '127.0.0.1',
    'charset' => 'utf0'
];

class Database
{
    private $db;
    private $tables;
    private $t;
    private static $instance = null;
    private $_connection;
    private $where = [];
    private $join = [];
    private $groupBy = [];
    private $order = [];
    private $limit = null;

    public static function instance()
    {
        return self::$instance == null ? self::$instance = new self() : self::$instance;
    }

    public function __construct()
    {
        $this->db = new PDO("mysql:dbname=test;host=127.0.0.1",
            DBConfig['user'],
            DBConfig['pass'],
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

        $this->_connection = new mysqli(DBConfig['host'],DBConfig['user'],DBConfig['pass'],DBConfig['name']);

        $this->tables = $this->db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getConnection() {
        return $this->_connection;
    }

    public function __get($name) {
        if (!in_array($name, $this->tables)) {
            die("Table not found!");
        }
        $this->t = $name;
        return $this;
    }

    public function limit($limit, $offset = null)
    {
        $this->limit = [$limit, $offset];
    }

    private function _join($table, $field, $linkTo = null, $alias = null, $type = "INNER")
    {
        if ($linkTo === null) {
            $linkTo = "`$this->t`"."`id`";
        } else {
            $linkTo = str_replace(".", "`.`", $linkTo);
        }

        $on = (empty($alias) ?"`{$table}`":"`{$alias}`") . ".`{$field}` = `{$linkTo}`}";
        $this->join[] = [$type, $table, $alias, $on];
    }

    public function join($table, $field, $linkTo = null, $alias = null)
    {
        $this->_join($table, $field, $linkTo, $alias);
        return $this;
    }

    public function leftJoin($table, $field, $linkTo = null, $alias = null)
    {
        $this->_join($table, $field, $linkTo, $alias, 'LEFT');
        return $this;
    }

    public function rightJoin($table, $field, $linkTo = null, $alias = null)
    {
        $this->_join($table, $field, $linkTo, $alias, 'RIGHT');
        return $this;
    }

    public function groupBy($field)
    {
        $this->groupBy[] = str_replace(".", '`.`', $field);
        return $this;
    }

    public function asc($field = "id")
    {
        $this->order[] = [str_replace(".", "`.`", $field), "ASC"];
        return $this;
    }

    public function desc($field = "id")
    {
        $this->order[] = [str_replace(".", "`.`", $field), "DESC"];
        return $this;
    }

    private function _where($type, $field, $sign, $value)
    {
        if($value === null) {
            $sign = "=";
            $value = $sign;
        }
        return [$type, str_replace(".", "`.`", $field), $sign, $value];
    }

    public function where($field, $sign, $value = null)
    {
        $this->where[] = $this->_where("", $field, $sign, $value);
        return $this;
    }

    public function andWhere($field, $sign, $value = null)
    {
        $this->where[] = $this->_where("AND", $field, $sign, $value);
        return $this;
    }

    public function orWhere($field, $sign, $value = null)
    {
        $this->where[] = $this->_where("OR", $field, $sign, $value);
        return $this;
    }

    private function _select($table, $wheres = [], $joins = null, $orders = null, $fields = "*", $group_by = null, $limit = null)
    {
        $q = "SELECT {$fields} FROM `{$table}`";

        if(!empty($joins)) {
            foreach ($joins as $join) {
                $q .= " {$join[0]} JOIN `{$join[1]}`";
                if(!empty($joins)) {
                    $q .= " AS `{$join[2]}`";
                }
                $q .= " ON `{$join[3]}`";
            }
        }

        if(!empty($wheres)) {
            $q .= " WHERE";
            foreach ($wheres as $where) {
                $q .= " {$where[0]}";
                if(count($where) > 1) {
                    $q .= " `{$where[1]}` {$where[2]} {$where[3]} ";
                }
            }
        }

        if(!empty($group_by)) {
            $q .= " GROUP BY {`".implode("`,`", $group_by)."`}";
        }

        if(!empty($orders)) {
            $q .= " ORDER BY";
            $tmp = [];
            foreach ($orders as $order) {
                $tmp[] = " ` {$order[0]}` {$order[1]}";
            }
            $q .= implode(",", $tmp);
        }

        if(!empty($limit)) {
            $q .= " LIMIT {$limit[0]}";
            if(!empty($limit[1])) {
                $q .= " OFFSET {$limit[1]}";
            }
        }

        return $q;
    }

    public function select($fields = "*")
    {
        if (is_array($fields)) {
            $fields = implode(",", $fields);
        }
        return $this->_select($this->t, $this->where, $this->join, $this->order, $fields, $this->groupBy, $this->limit);
    }
}