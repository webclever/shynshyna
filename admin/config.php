<?php
// HTTP
$host = 'http://localhost/shynshyna/';
define('HTTP_SERVER', $host.'admin/');
define('HTTP_CATALOG', $host);

// HTTPS
define('HTTPS_SERVER', $host.'admin/');
define('HTTPS_CATALOG', $host);

// DIR
$dir = 'C:\xampp\htdocs\shynshyna\\';
define('DIR_APPLICATION', $dir . 'admin/');
define('DIR_SYSTEM', $dir . 'system/');
define('DIR_LANGUAGE', $dir . 'admin/language/');
define('DIR_TEMPLATE', $dir . 'admin/view/template/');
define('DIR_CONFIG', $dir . 'system/config/');
define('DIR_IMAGE', $dir . 'image/');
define('DIR_CACHE', $dir . 'system/cache/');
define('DIR_DOWNLOAD', $dir . 'system/download/');
define('DIR_UPLOAD', $dir . 'system/upload/');
define('DIR_LOGS', $dir . 'system/logs/');
define('DIR_MODIFICATION', $dir . 'system/modification/');
define('DIR_CATALOG', $dir . 'catalog/');


// // HTTP
// define('HTTP_SERVER', 'http://shynshyna.com.ua/admin/');
// define('HTTP_CATALOG', 'http://shynshyna.com.ua/');

// // HTTPS
// define('HTTPS_SERVER', 'http://shynshyna.com.ua/admin/');
// define('HTTPS_CATALOG', 'http://shynshyna.com.ua/');

// // DIR
// define('DIR_APPLICATION', '/var/www/webclever/data/www/shynshyna.com.ua/admin/');
// define('DIR_SYSTEM', '/var/www/webclever/data/www/shynshyna.com.ua/system/');
// define('DIR_LANGUAGE', '/var/www/webclever/data/www/shynshyna.com.ua/admin/language/');
// define('DIR_TEMPLATE', '/var/www/webclever/data/www/shynshyna.com.ua/admin/view/template/');
// define('DIR_CONFIG', '/var/www/webclever/data/www/shynshyna.com.ua/system/config/');
// define('DIR_IMAGE', '/var/www/webclever/data/www/shynshyna.com.ua/image/');
// define('DIR_CACHE', '/var/www/webclever/data/www/shynshyna.com.ua/system/cache/');
// define('DIR_DOWNLOAD', '/var/www/webclever/data/www/shynshyna.com.ua/system/download/');
// define('DIR_UPLOAD', '/var/www/webclever/data/www/shynshyna.com.ua/system/upload/');
// define('DIR_LOGS', '/var/www/webclever/data/www/shynshyna.com.ua/system/logs/');
// define('DIR_MODIFICATION', '/var/www/webclever/data/www/shynshyna.com.ua/system/modification/');
// define('DIR_CATALOG', '/var/www/webclever/data/www/shynshyna.com.ua/catalog/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'shynshyna_db');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');
