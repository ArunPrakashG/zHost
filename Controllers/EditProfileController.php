<?php
require_once '../Core/Config.php';
require_once '../Core/UserModel.php';
require_once '../Common/Functions.php';
require_once '../Core/SessionCheck.php';
require_once '../Core/DatabaseManager.php';

if (Config::DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
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
    'FilePath' => ''
);

function SetResult($message, $reason, $status, $level, $avatarFilePath = NULL)
{
    global $Result;
    $Result['ShortReason'] = $message;
    $Result['Reason'] = $reason;
    $Result['Status'] = $status;
    $Result['Level'] = $level;

    if (isset($avatarFilePath)) {
        $Result['FilePath'] = $avatarFilePath;
    }
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

function ValidateForm()
{
    if ($_POST['passWord'] != $_POST['confirmPassword']) {
        SetResult("Password doesn't match!", "Both passwords do not match.", "-1", "warning");
        return false;
    }

    if (strlen($_POST['passWord']) < 4) {
        SetResult("Invalid Password!", "Passwords must have atleast 4 charecters!", "-1", "warning");
        return false;
    }

    if (preg_match('~[0-9]+~', $_POST['firstName'])) {
        SetResult("Invalid First Name!", "Name should not contain Numbers!", "-1", "warning");
        return false;
    }

    if (preg_match('~[0-9]+~', $_POST['lastName'])) {
        SetResult("Invalid Last Name!", "Name should not contain Numbers!", "-1", "warning");
        return false;
    }

    if (!preg_match('~[0-9]+~', $_POST['phoneNumber']) || preg_match('/\s/', $_POST['phoneNumber'])) {
        SetResult("Invalid Phone number!", "Phone number can only be numbers! (no whitespaces allowed)", "-1", "warning");
        return false;
    }

    if ($_POST['phoneNumber'] == "0") {
        SetResult("Invalid Phone number!", "There must be 10 charecters for a phone number!", "-1", "warning");
        return false;
    }

    if ($_POST['address'] == null) {
        SetResult("Invalid Address!", "Address can't be empty!", "-1", "warning");
        return false;
    }

    if (preg_match('/\s/', $_POST['passWord'])) {
        SetResult("Password is Invalid!", "Passwords should not contain whitespace.", "-1", "warning");
        return false;
    }

    return true;
}

function OnUserUpdateRequestReceived()
{    
    // assign default
    $serverAssignedPath = GetAndProcessFile('profilePicture');
    $avatarFilePath = "../includes/images/default-avatar.png";
    if (isset($_FILES) && isset($_FILES['profilePicture'])) {
        if (isset($serverAssignedPath) && !empty($serverAssignedPath)) {
            $moveResult = move_uploaded_file(
                $_FILES['profilePicture']['tmp_name'],
                $serverAssignedPath
            );

            $avatarFilePath = $moveResult ? $serverAssignedPath : "../includes/images/default-avatar.png";
        }
    }

    SetResult("File received!", "File received and saved.", "0", "success", $avatarFilePath);

    if (!ValidateForm()) {
        return;
    }

    $Db = new Database;
    if (!$Db->IsExistingUser($_POST["email"])) {
        SetResult("User doesn't exist!", "Such a user doesn't exist! Register first.", "-1", "error");
        return;
    }

    $_POST['avatarPath'] = $avatarFilePath;
    $_POST['password'] =  password_hash($_POST["passWord"], PASSWORD_DEFAULT);
    if ($Db->UpdateUser($_POST['email'], $_POST)) {
        SetResult("Success!", "Successfully updated your profile!", "0", "success");
        return;
    }

    SetResult("Failed!", "An internal server error occured while processing your request.", "-1", "error");
}

function OnUserSessionClearRequestReceived(){
    unset($_SESSION["userDetails"]);
    SetResult("Session Cleared!", "Successfully cleared your session, Please relogin again!", "0", "success");
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    SetResult("Invalid request type.", "Expected: POST", "-1", "error");
    echo json_encode($Result);
    exit();
}

// since its ajax, we should 'print' the return value as its an http request and there is no concept of datatype in these requests
// only plain raw string formate, so return as string of the respective type and parse on client side
switch ($_POST['requestType']) {
    case "update_user":
        OnUserUpdateRequestReceived();
        break;
    case "clear_user_session":
        OnUserSessionClearRequestReceived();
        break;
    default:
        SetResult("Invalid!", "Unknown request type.", "-1", "error");
        break;
}

echo json_encode($Result);
?>