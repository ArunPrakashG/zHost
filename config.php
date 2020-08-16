<?php
    session_start();
    $HOST="localhost";
    $DB_USER="root";
    $DB_PASS="root";
    $DB_NAME="OnlineBlog";
	$connection = mysqli_connect($HOST, $DB_USER, $DB_PASS, $DB_NAME) or die("Error connecting to database: " . mysqli_connect_error());

	define ('ROOT_PATH', realpath(dirname(__FILE__)));
    define('BASE_URL', 'http://localhost/onlineblog/');
?>
