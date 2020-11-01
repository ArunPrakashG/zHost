<?php

require_once '../Common/Functions.php';
require_once '../Core/Config.php';

if (!isset($_SESSION)) {
	session_start();
}

if (Config::DEBUG) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

if (array_key_exists('loginBttn', $_POST)) {
	$_SESSION['rq'] = "login";
	Functions::Redirect("../Views/LoginView.php?refer=index");
	exit();
}

if (array_key_exists('registerBttn', $_POST)) {
	$_SESSION['rq'] = "register";
	Functions::Redirect("../Views/RegisterView.php?refer=index");
	exit();
}
?>