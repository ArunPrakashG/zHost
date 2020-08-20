<?php
require_once '../Common/Functions.php';
require_once '../Core/Config.php';
require_once '../Core/DatabaseManager.php';

if (Config::DEBUG) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

if (!isset($_SESSION)) {
	session_start();
}

function validateRegisterForum()
{
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		Functions::Alert("Invalid request type.");
		Functions::Redirect("../Views/RegisterView.php");
		return false;
	}

	if (!isset($_POST["email"])) {
		Functions::Alert("Email is invalid or empty.");
		Functions::Redirect("../Views/RegisterView.php");
		return false;
	}

	if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		Functions::Alert("Invalid email. Email must end with @zhost.com domain.");
		Functions::Redirect("../Views/RegisterView.php");
		return false;
	}

	if (!isset($_POST["username"])) {
		Functions::Alert("Username is invalid or empty.");
		Functions::Redirect("../Views/RegisterView.php");
		return false;
	}

	if (!isset($_POST["psw"])) {
		Functions::Alert("Password is invalid or empty.");
		Functions::Redirect("../Views/RegisterView.php");
		return false;
	}

	if (preg_match('/\s/', $_POST["psw"])) {
		Functions::Alert("Password should not contain whitespace charecters.");
		Functions::Redirect("../Views/RegisterView.php");
		return false;
	}

	if (!isset($_POST['psw-repeat'])) {
		Functions::Alert("Please re-enter the password to confirm.");
		Functions::Redirect("../Views/RegisterView.php");
		return false;
	}

	if (strcmp($_POST["psw"], $_POST["psw-repeat"]) != 0) {
		Functions::Alert("Passwords doesn't match.");
		Functions::Redirect("../Views/RegisterView.php");
		return false;
	}

	return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (!validateRegisterForum()) {
		exit();
	}

	$Db = new Database;

	if ($Db->IsExistingUser($_POST["email"])) {
		Functions::Alert("An account with this email id already exist!\nTry again with different mail id.");
		Functions::Redirect("../Views/RegisterView.php");
		exit();
	}

	$Password = password_hash($_POST["psw"], PASSWORD_DEFAULT);
	if ($Db->RegisterUser($_POST['username'], $_POST['email'], $Password, false)) {
		Functions::Alert("Registeration successfull!\nYou will be redirected to login page now...");
		Functions::Redirect("../Views/RedirectView.php?path=../Views/LoginView.php&name=Login Page&header=Login");
		exit();
	}
}

?>