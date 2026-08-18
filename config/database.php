<?php

return [
    'host'     => getenv('DB_HOST') ?: '127.0.0.1',
    'dbname'   => getenv('DB_NAME') ?: 'automationweek_9',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'password',
    'charset'  => 'utf8mb4'
];

