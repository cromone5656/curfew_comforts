<?php

// Load environment variables from .env file
require __DIR__ . '/vendor/autoload.php'; // Composer autoloader
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

return [
    'paths' => [
        'migrations' => './db/migrations',
        'seeds' => './db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? getenv('DB_HOST'), // Safely read the variable
            'name' => $_ENV['DB_NAME'] ?? getenv('DB_NAME'),
            'user' => $_ENV['DB_USER'] ?? getenv('DB_USER'),
            'pass' => $_ENV['DB_PASS'] ?? getenv('DB_PASS'),
            'port' => 3306,
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? getenv('DB_HOST'),
            'name' => $_ENV['DB_NAME'] ?? getenv('DB_NAME'),
            'user' => $_ENV['DB_USER'] ?? getenv('DB_USER'),
            'pass' => $_ENV['DB_PASS'] ?? getenv('DB_PASS'),
            'port' => 3306,
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
