<?php

namespace App\Model;

use IrfanTOOR\Database\Model;

class Sessions extends Model
{
    function __construct($connection = [])
    {
        $table = isset($connection['table']) ? $connection['table'] : null;
        
        if (!isset($connection['file'])) {
            $connection['file'] = ROOT . 'storage/db/sessions.sqlite';
        }
    
        $this->schema = [
            'id INTEGER PRIMARY KEY',

            'sid',
            'value',

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at INTEGER',
        ];

        $this->indecies = [
            ['unique' => 'sid'],
            ['index'  => 'created_at'],
            ['index'  => 'updated_at'],
        ];

        parent::__construct($connection, $table);
    }
}
