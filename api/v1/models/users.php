<?php

namespace V1\Models;

class Users extends \Base\Model
{
    protected $table  = 'users';

    protected $fields = [
        'uid'      => ['type' => 'VARCHAR128', 'nullable' => false],
        'email'    => ['type' => 'VARCHAR128', 'nullable' => false],
        'username' => ['type' => 'VARCHAR128', 'nullable' => false],
        'password' => ['type' => 'VARCHAR512', 'nullable' => false],
        'rights'   => ['type' => 'JSON'],
        'created'  => ['type' => 'TIMESTAMP'],
        'updated'  => ['type' => 'TIMESTAMP'],
        'deleted'  => ['type' => 'BOOLEAN']
    ];

    function set_password($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }
}