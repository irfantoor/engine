<?php

namespace App\Model;

use IrfanTOOR\Database\Model;

class Users extends Model
{
    function __construct($connection = [])
    {
        $table = isset($connection['table']) ? $connection['table'] : null;
        if (!isset($connection['file'])) {
            $connection['file'] = ROOT . 'storage/db/users.sqlite';
        }
        
        $this->schema = [
            'id INTEGER PRIMARY KEY',

            'name NOT NULL',
            'email COLLATE NOCASE NOT NULL',
            'password NOT NULL',
            'token',
            'validated BOOL DEFAULT 0',

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at INTEGER',
        ];

        $this->indecies = [
            ['index'  => 'name'],
            ['unique' => 'email'],
            ['index'  => 'validated']
        ];

        parent::__construct($connection, $table);
    }

    function authenticate($email, $password)
    {
        $authenticated = false;

        $user = $this->getFirst(
            ['where' => 'email = :email and validated = :validated'],
            ['email' => $email, 'validated' => 1]
        );

        if ($user) {
            $authenticated = (md5($password) === $user['password']) ? true : false;
        }

        // sleep(2);
        return $authenticated;
    }

    function register($user)
    {
        try {
            $this->insert($user);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
