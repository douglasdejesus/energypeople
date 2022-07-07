<?php

define('URL_BASE', 'http://localhost/energy-people');

define('DS', DIRECTORY_SEPARATOR);
define('PATH_LOG', __DIR__ . DS . 'Log' . DS . 'log.log');
define('PATH_UPLOAD', dirname(__DIR__, 1));
define('PADRAO_BD', 'DB');
define('PADRAO_BRASILEIRO', 'BRL');

const DATA_LAYER_CONFIG = [
	'driver' => 'mysql',
	'host' => 'localhost',
	'port' => '3306',
	'dbname' => 'energy_people',
	'username' => 'root',
	'passwd' => 'root',
	'options' => [
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
		PDO::ATTR_CASE => PDO::CASE_NATURAL
	]
];

