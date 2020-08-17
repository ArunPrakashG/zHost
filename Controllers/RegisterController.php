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
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"] = "Invalid request type.";
			return false;
		}

		if (!isset($_POST["email"])) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"]  = "Email cannot be empty!";
			return false;
		}

		if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"]  = "Email is invalid.";
			return false;
		}

		if (!isset($_POST["username"])) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"]  = "Username cannot be empty!";
			return false;
		}

		if (!isset($_POST["psw"])) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"]  = "Password cannot be empty!";
			return false;
		}

		if (preg_match('/\s/', $_POST["psw"])) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"]  = "Password should not contain white spaces.";
			return false;
		}

		if (!isset($_POST['psw-repeat'])) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"]  = "Please repeat the password.";
			return false;
		}

		if (strcmp($_POST["psw"] , $_POST["psw-repeat"]) != 0) {
			$_SESSION["registerErrorMessage"] = "Passwords doesn't match.";
			$_SESSION["IsError"] = true;
			return false;
		}

		$_SESSION["IsError"] = false;
		unset($_SESSION["registerErrorMessage"]);
		return true;
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (!validateRegisterForum()) {
			if (!isset($_SESSION["registerErrorMessage"])) {
				$_SESSION["registerErrorMessage"] = "Failed to register. try again.";
			}

            Functions::Redirect("../Views/RegisterView.php");
			exit();
		}

		$Db = new Database;

		if ($Db->IsExistingUser($_POST["email"])) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"] = "The specified email id already exists. Please choose another one.";
			Functions::Redirect("../Views/RegisterView.php");
			exit();
		}

		$Password = password_hash($_POST["psw"], PASSWORD_DEFAULT);
		if ($Db->RegisterUser($_POST['username'], $_POST['email'], $Password, false)) {
			$_SESSION["IsError"] = false;
			unset($_SESSION["registerErrorMessage"]);
			$_SESSION["RegistrationMessage"] = "Registration successfull! Please login!";
            Functions::Redirect("../Views/LoginRedirectView.php");       
			exit();
		}
	}

?>