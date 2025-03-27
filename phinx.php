<?php

return
    [
        'paths' => [
            'migrations' => './db/migrations',
            'seeds' => './db/seeds'
        ],
        'environments' => [
            'default_migration_table' => 'phinxlog',
            'default_environment' => 'development',
            'production' => [
                'adapter' => 'mysql',
                'host' => getenv('DB_HOST') ?? 'localhost',
                'name' => getenv('DB_NAME') ?? 'production_db',
                'user' => getenv('DB_USER') ?? 'root',
                'pass' => getenv('DB_PASS') ?? '',
                'port' => 3306,
                'charset' => 'utf8',
            ],
            'development' => [
                'adapter' => 'mysql',
                'host' => 'db',
                'name' => 'curfew_comforts',
                'user' => 'root',
                'pass' => 'root',
                'port' => '3306',
                'charset' => 'utf8',
            ]
        ],
        'version_order' => 'creation'
    ];
