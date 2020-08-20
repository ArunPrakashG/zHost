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

function ValidateRegisterForum()
{
	if (!isset($_POST["email"])) {
		//Functions::Alert("Email is invalid or empty.");
		//Functions::Redirect("../Views/RegisterView.php");
		return "10";
	}

	if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		//Functions::Alert("Invalid email. Email must end with @zhost.com domain.");
		//Functions::Redirect("../Views/RegisterView.php");
		return "11";
	}

	if (!isset($_POST["username"])) {
		//Functions::Alert("Username is invalid or empty.");
		//Functions::Redirect("../Views/RegisterView.php");
		return "12";
	}

	if (!isset($_POST["psw"])) {
		//Functions::Alert("Password is invalid or empty.");
		//Functions::Redirect("../Views/RegisterView.php");
		return "13";
	}

	if (preg_match('/\s/', $_POST["psw"])) {
		//Functions::Alert("Password should not contain whitespace charecters.");
		//Functions::Redirect("../Views/RegisterView.php");
		return "14";
	}

	if (!isset($_POST['psw-repeat'])) {
		//Functions::Alert("Please re-enter the password to confirm.");
		//Functions::Redirect("../Views/RegisterView.php");
		return "15";
	}

	if (strcmp($_POST["psw"], $_POST["psw-repeat"]) != 0) {
		//Functions::Alert("Passwords doesn't match.");
		//Functions::Redirect("../Views/RegisterView.php");
		return "16";
	}

	if(!isset($_POST["pnumber"])){
		return "17";
	}

	if(mb_strlen($_POST["pnumber"]) != 10){
		return "18";
	}

	return "0";
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
	echo "-1";
	exit();
}

function OnRegisterRequestReceived()
{
	$_POST['email'] = $_POST['postData'][0]['value'];
	$_POST['username'] = $_POST['postData'][1]['value'];
	$_POST['psw'] = $_POST['postData'][2]['value'];
	$_POST['psw-repeat'] = $_POST['postData'][3]['value'];
	$_POST['pnumber'] = preg_replace('/\s+/', ' ', $_POST['postData'][4]['value'] ?? "1");
	$_POST['secquest'] = $_POST['postData'][5]['value'] ?? "";
	$_POST['secans'] = $_POST['postData'][6]['value'] ?? "";

	$validationResult = ValidateRegisterForum();

	if ($validationResult != 0) {
		return $validationResult;
	}

	$Db = new Database;

	if ($Db->IsExistingUser($_POST["email"])) {
		//Functions::Alert("An account with this email id already exist!\nTry again with different mail id.");
		//Functions::Redirect("../Views/RegisterView.php");
		return "1";
	}

	$Password = password_hash($_POST["psw"], PASSWORD_DEFAULT);
	if ($Db->RegisterUser($_POST['username'], $_POST['email'], $Password, false)) {
		//Functions::Alert("Registeration successfull!\nYou will be redirected to login page now...");
		//Functions::Redirect("../Views/RedirectView.php?path=../Views/LoginView.php&name=Login Page&header=Login");
		return "0";
	}
}

switch ($_POST['requestType']) {
	case "register":
		echo OnRegisterRequestReceived();
		break;
}
?>