<?php

namespace App\Model;

use IrfanTOOR\Database\Model;

class Test extends Model
{
    function __construct($connection = [])
    {
        $table = isset($connection['table']) ? $connection['table'] : null;
        
        if (!isset($connection['file'])) {
            $connection['file'] = ROOT . 'storage/db/test.sqlite';
        }
        
        $connection = [
            'table' => $table,
            'file'  => $file,
        ];
    
        $this->schema = [
            'id INTEGER PRIMARY KEY',
            'key',
            'value',

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at INTEGER',
        ];

        $this->indecies = [
            ['unique' => 'key'],
            ['index'  => 'updated_at'],
        ];

        parent::__construct($connection);
    }
}
