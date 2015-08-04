<?php
// HTTP
$host = 'http://localhost/shynshyna/';
define('HTTP_SERVER', $host.'/');
define('HTTP_IMAGE', $host.'/image/');
define('HTTP_ADMIN', $host.'/admin/');

// HTTPS
define('HTTPS_SERVER', $host.'/');
define('HTTPS_IMAGE', $host.'/image/');

// DIR
$dir = 'C:\xampp\htdocs\shynshyna';
define('DIR_APPLICATION', $dir . '/catalog/');
define('DIR_SYSTEM', $dir . '/system/');
define('DIR_LANGUAGE', $dir . '/catalog/language/');
define('DIR_TEMPLATE', $dir . '/catalog/view/theme/');
define('DIR_CONFIG', $dir . '/system/config/');
define('DIR_IMAGE', $dir . '/image/');
define('DIR_CACHE', $dir . '/system/cache/');
define('DIR_DOWNLOAD', $dir . '/system/download/');
define('DIR_UPLOAD', $dir . '/system/upload/');
define('DIR_MODIFICATION', $dir . '/system/modification/');
define('DIR_LOGS', $dir . '/system/logs/');

// // HTTP
// define('HTTP_SERVER', 'http://shynshyna.com.ua/');

// // HTTPS
// define('HTTPS_SERVER', 'http://shynshyna.com.ua/');

// // DIR
// define('DIR_APPLICATION', '/var/www/webclever/data/www/shynshyna.com.ua/catalog/');
// define('DIR_SYSTEM', '/var/www/webclever/data/www/shynshyna.com.ua/system/');
// define('DIR_LANGUAGE', '/var/www/webclever/data/www/shynshyna.com.ua/catalog/language/');
// define('DIR_TEMPLATE', '/var/www/webclever/data/www/shynshyna.com.ua/catalog/view/theme/');
// define('DIR_CONFIG', '/var/www/webclever/data/www/shynshyna.com.ua/system/config/');
// define('DIR_IMAGE', '/var/www/webclever/data/www/shynshyna.com.ua/image/');
// define('DIR_CACHE', '/var/www/webclever/data/www/shynshyna.com.ua/system/cache/');
// define('DIR_DOWNLOAD', '/var/www/webclever/data/www/shynshyna.com.ua/system/download/');
// define('DIR_UPLOAD', '/var/www/webclever/data/www/shynshyna.com.ua/system/upload/');
// define('DIR_MODIFICATION', '/var/www/webclever/data/www/shynshyna.com.ua/system/modification/');
// define('DIR_LOGS', '/var/www/webclever/data/www/shynshyna.com.ua/system/logs/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'shynshyna_db');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');
