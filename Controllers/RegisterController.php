<?php
require_once '../Common/Functions.php';
require_once '../Core/Config.php';
require_once '../Core/DatabaseManager.php';

if (Config::DEBUG) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	ini_set("log_errors", TRUE);
}

if (!isset($_SESSION)) {
	session_start();
}

header("Content-Type", "application/json");
$Result = array(
	'ShortReason' => 'NA',
	'Reason' => 'NA',
	'Status' => '-1',
	'Level' => 'warning',
	'FilePath' => '../includes/images/default-avatar.png'
);

function SetResult($message, $reason, $status, $level, $filePath = NULL)
{
	global $Result;
	$Result['ShortReason'] = $message;
	$Result['Reason'] = $reason;
	$Result['Status'] = $status;
	$Result['Level'] = $level;

	if (isset($filePath)) {
		$Result['FilePath'] = $filePath;
	}
}

function ValidateRegisterForum()
{
	if (!isset($_POST["email"])) {
		SetResult("Email is empty", "Recheck entered email.", "-1", "warning");
		return false;
	}

	if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		SetResult("Invalid email!", "Email must be under @zhost.com domain.", "-1", "warning");
		return false;
	}

	if (!isset($_POST["username"])) {
		SetResult("Username is empty!", "Recheck entered username.", "-1", "warning");
		return false;
	}

	if (!isset($_POST["psw"])) {
		SetResult("Password is empty!", "Recheck entered password.", "-1", "warning");
		return false;
	}

	if (preg_match('/\s/', $_POST["psw"])) {
		SetResult("Password is invalid!", "Password should not contain whitespace charecters.", "-1", "warning");
		return false;
	}

	if (mb_strlen($_POST['psw']) < 4) {
		SetResult("Password is Invalid!", "Password must have atleast 4 chareceters, should not be whitespace", "-1", "warning");
		return false;
	}

	if (!isset($_POST['psw-repeat'])) {
		SetResult("Re-enter password to confirm", "You havnt re entered password.", "-1", "warning");
		return false;
	}

	if (strcmp($_POST["psw"], $_POST["psw-repeat"]) != 0) {
		SetResult("Passwords don't match!", "Recheck your typed password.", "-1", "warning");
		return false;
	}

	if (!isset($_POST["pnumber"])) {
		SetResult("Phone number is empty!", "Recheck entered phone number.", "-1", "warning");
		return false;
	}

	if (mb_strlen($_POST["pnumber"]) != 10) {
		SetResult("Invalid Phone number!", "Enter a valid phone number with a length of 10 digits.", "-1", "warning");
		return false;
	}

	SetResult("Form validation success", "Validation success", "0", "success");
	return true;
}

function GetAndProcessFile($requestFileName)
{
	if (!isset($requestFileName)) {
		return "";
	}

	if (isset($_FILES) && isset($_FILES[$requestFileName])) {
		if ($_FILES[$requestFileName]['error'] == 0 && $_FILES[$requestFileName]['size'] > 0) {
			if (file_exists('../Core/Uploads/' . $_FILES[$requestFileName]['name'])) {
				$split = explode('.', $_FILES[$requestFileName]['name']);
				if (count($split) != 2) {
					// not possible
					return '../Core/Uploads/' . $_FILES[$requestFileName]['name'];
				}

				return '../Core/Uploads/' . $split[0] . ' (' . rand(1, 500) . ')' . '.' . $split[1];
			}

			return '../Core/Uploads/' . $_FILES[$requestFileName]['name'];
		}
	}

	return "";
}

function OnRegisterRequestReceived()
{
	// assign default
	$serverAssignedPath = GetAndProcessFile('profileimage');
	$avatarFilePath = "../includes/images/default-avatar.png";
	if (isset($_FILES) && isset($_FILES['profileimage'])) {
		if (isset($serverAssignedPath) && !empty($serverAssignedPath)) {
			$moveResult = move_uploaded_file(
				$_FILES['profileimage']['tmp_name'],
				$serverAssignedPath
			);

			$avatarFilePath = $moveResult ? $serverAssignedPath : "../includes/images/default-avatar.png";
		}
	}

	SetResult("File received!", "File received and saved.", "0", "success", $avatarFilePath);

	if (!ValidateRegisterForum()) {
		return;
	}

	$Db = new Database;
	if ($Db->IsExistingUser($_POST["email"])) {
		SetResult("Account exist!", "An account with this email id already exist.", "-1", "warning");
		return;
	}

	$_POST['password'] =  password_hash($_POST["psw"], PASSWORD_DEFAULT);

	if ($Db->RegisterUser($_POST, $avatarFilePath, false)) {
		SetResult("Account Registered!", "You will be redirected to login page now...", "0", "success");
		return;
	}

	SetResult("Registration failed.", "Registration failed.", "-1", "error");
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
	SetResult("Invalid request type.", "Expected: POST", "-1", "error");
	echo json_encode($Result);
	exit();
}

switch ($_POST['requestType']) {
	case "register":
		OnRegisterRequestReceived();

		if ($Result['Status'] != "0") {
			$_SESSION['form-data']['email'] = $_POST['email'];
			$_SESSION['form-data']['pnumber'] = $_POST['pnumber'];
			$_SESSION['form-data']['username'] = $_POST['username'];
			$_SESSION['form-data']['secquest'] = $_POST['secquest'];
			$_SESSION['form-data']['secans'] = $_POST['secans'];
			$_SESSION['form-data']['psw'] = $_POST['psw'];
			$_SESSION['form-data']['psw-repeat'] = $_POST['psw-repeat'];
		}

		echo json_encode($Result);
		break;
}
?>