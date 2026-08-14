<?php

date_default_timezone_set('Asia/Jakarta');

define('APP_NAME', 'Automation Week 9');
define('BASE_PATH', dirname(__DIR__));

foreach ((array) @parse_ini_file(BASE_PATH . '/.env') as $key => $value) {
    putenv("$key=$value");
}
