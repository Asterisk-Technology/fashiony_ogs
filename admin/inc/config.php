<?php
// Error Reporting Turn On
ini_set('error_reporting', E_ALL);

// Setting up the time zone
date_default_timezone_set('Asia/Dubai');

// Host Name
$dbhost = '127.0.0.1';

// Database Name
$dbname = 'fashiony_ogs';

// Database Username
$dbuser = 'fashiony_user';

// Database Password
$dbpass = 'secure_password123';
$port = $_SERVER['SERVER_PORT'];

$host = $_SERVER['HTTP_HOST'];

// Defining base url
define("BASE_URL", "http://{$host}/");

// Getting Admin url
define("ADMIN_URL", BASE_URL . "admin" . "/");

try {
	$pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch( PDOException $exception ) {
	echo "Connection error :" . $exception->getMessage();
}