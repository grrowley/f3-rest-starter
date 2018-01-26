<?php

namespace Base;

use \DB\Cortex;

abstract class Model extends Cortex
{
    protected
        $table  = '',
        $fields = [];

    function __construct()
    {
        $app = \Base::instance();
        if ($app->exists('DATA', $db)) {
            switch ($db['type']) {
                case 'sqlite':
                    $this->db = new \DB\SQL("sqlite:SERVER={$db['host']}");
                    break;
                case 'pgsql':
                    $this->db = new \DB\SQL("pgsql:host={$db['host']};dbname={$db['name']}", $db['user'], $db['pswd']);
                    break;
                case 'sqlsrv':
                    $this->db = new \DB\SQL("sqlsrv:SERVER={$db['host']};Database={$db['name']}", $db['user'], $db['pswd']);
                    break;
                case 'jig':
                    $this->db = new \DB\Jig($db['host']);
                    break;
                case 'mongo':
                    $this->db = new \DB\Mongo($db['host'], $db['name']);
                    break;
                default:
                    $this->db = new \DB\SQL("mysql:host={$db['host']};port={$db['port']};dbname={$db['name']}", $db['user'], $db['pswd']);
                    break;
            }
            $this->table = $db['pfix'] . $this->table;
        }
        if (is_array($this->fields) && !empty($this->fields)) {
            $this->fieldConf = $this->fields;
        }
        parent::__construct();
    }

    function clean($data)
    {
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $this->fieldConf)) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    function set_uid($value)
    {
        if ($value === true) {
            return \Base::instance()->hash(microtime(true));
        }
        return $value;
    }
}