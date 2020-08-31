<?php
require_once '../Common/Functions.php';
require_once '../Core/Config.php';
require_once '../Core/DatabaseManager.php';
require_once '../Core/SessionCheck.php';
require_once '../Core/UserModel.php';

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
    'SecData' => ''
);

function SetResult($message, $reason, $status, $level, $secData = null)
{
    global $Result;
    $Result['ShortReason'] = $message;
    $Result['Reason'] = $reason;
    $Result['Status'] = $status;
    $Result['Level'] = $level;

    if(isset($secData)){
        $Result['SecData'] = $secData;
    }    
}

function ValidateLoginForum()
{
    if (!isset($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        SetResult("Email is invalid.", "Emails must not contain whitespace charecters and they should be under @zhost.com domain.", "-1", "warning");
        return false;
    }

    if (!isset($_POST["password"])) {
        SetResult("Password is empty.", "Password must not be an empty charecter.", "-1", "warning");
        return false;
    }

    SetResult("Validation success", "Success", "0", "success");
    return true;
}

function OnLogoutRequestReceived()
{
    if (!IsUserLoggedIn()) {
        SetResult("You are not logged in!", "Unauthorized.", "-1", "error");
        return false;
    }

    // these variables are part of the user session
    // they are not required when session is no longer valid, ie, when user logs out
    unset($_SESSION['Trash']);
    unset($_SESSION['Draft']);
    unset($_SESSION['Inbox']);
    unset($_SESSION['userDetails']);
    unset($_SESSION['USER_NAME']);
    unset($_SESSION['ID']);
    
    // handling redirection and alert in client side for the animated alert to display
    SetResult("Success!", "You will be redirected to Login page.", "0", "success");
    return true;
}

function OnLoginRequestReceived()
{
    $_POST['email'] = $_POST['postData'][0]['value'];
    $_POST['password'] = $_POST['postData'][1]['value'];

    if (!ValidateLoginForum()) {
        return;
    }

    $Db = new Database;

    if (!$Db->IsExistingUser($_POST["email"])) {
        SetResult("User doesn't exist!", "Such a user doesn't exist.", "-1", "error");
        return false;
    }

    if ($loginResult = $Db->LoginUser($_POST["email"], $_POST["password"], false)) {
        if (isset($loginResult['isError']) && $loginResult['isError']) {
            SetResult("Error!", "Password is wrong.", "-1", "error");
            return false;
        }

        $loginResultObj = unserialize($loginResult['resultObj']);
        $_SESSION['userDetails'] = $loginResult['resultObj'];
        $_SESSION['ID'] = $loginResultObj->Id;
        $_SESSION['USER_NAME'] = $loginResultObj->UserName;
        SetResult("Welcome to zHost!", "You are successfully logged in!", "0", "success");
        return true;
    }
}

function OnSecurityDataRequestReceived(){
    if(!isset($_POST['email'])){
        SetResult("Invalid email!", "Email is empty or invalid!", "-1", "error");
        return;
    }

    $Db = new Database;
    if($secData = $Db->GetUserSecurityData($_POST['email'])){
        SetResult("Success!", "Security data fetched.", "0", "success", $secData['Data']);        
        return;
    }

    SetResult("Failed!", "Failed to fetch security data (Check database connection)", "-1", "error");
}

function OnRecoveryPasswordRequestReceived(){
    if(!isset($_POST['new_pass']) || !isset($_POST['email'])){
        SetResult("Invalid Password/Email!", "Password or Email appears to be empty.", "-1", "warning");
        return;
    }

    $Db = new Database;
    if($Db->UpdateUserPassword($_POST['email'], $_POST['new_pass'])){
        SetResult("Success!", "Password updated.", "0", "success");
        return;
    }

    SetResult("Failed!", "Failed to update password. (Check database connection)", "-1", "error");
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    SetResult("Invalid request type.", "Expected: POST", "-1", "error");
    echo json_encode($Result);
    exit();
}

switch ($_POST['requestType']) {
    case "logout":
        OnLogoutRequestReceived();
        break;
    case "login":
        OnLoginRequestReceived();        
        break;
    case "recovery_security_data":
        OnSecurityDataRequestReceived();
        break;
    case "recovery_set_password":
        OnRecoveryPasswordRequestReceived();
        break;
}

echo json_encode($Result);
?>