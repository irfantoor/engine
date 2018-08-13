<?php

return [
    # Application configuration
    'debug' => [
        'level' =>           3,            # 0: no, 1, 2, 3
    ],

    'cache' => [
        'active'             => false,
        'folder'             => 'storage/cache/',
        'db'                 => 'storage/db/cache.sqlite',
        'tmp'                => 'storage/tmp/',
        'queries'            => true,      # queries are cached
        'images'             => true,      # dynamic images are cached
        'life'               => 0,         # never expires 10*60 # 10 minutes
    ],

    'log' => [
        'active'             => false,
        'level'              => 1,         # 0: error, 1: warning, 2: notices, 3
    ],

    'database' => [
        'mysql' => [
            'host'   => '127.0.0.1',
            'user'   => 'root',
            'pass'   => 'toor',
            'dbname' => 'mydb',
        ],
        'sqlite' => [
            'folder' => 'storage/db/',
        ]
    ],
    
    'session' => [
        # md5('irfantoor.com/session')
        'key' => '632047d577526669bcb75f1c288c65da'
    ],
];
