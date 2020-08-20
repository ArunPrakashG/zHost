<?php
require_once '../Common/Functions.php';
require_once '../Core/Config.php';
require_once '../Core/DatabaseManager.php';
require_once '../Core/SessionCheck.php';

if (Config::DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (!isset($_SESSION)) {
    session_start();
}

function ValidateLoginForum()
{
    if (!isset($_POST["email"])) {
        //Functions::Alert("Username is invalid or empty.");
        //Functions::Redirect("../Views/LoginView.php");
        return "10";
    }

    if (!isset($_POST["password"])) {
        //Functions::Alert("Password is invalid or empty.");
        //Functions::Redirect("../Views/LoginView.php");
        return "11";
    }

    return "0";
}

function OnLogoutRequestReceived()
{
    if (!IsUserLoggedIn()) {
        //Functions::Alert("Session expired!\nYou will be required to login again.");
        //Functions::Redirect("../Views/RedirectView.php?path=../Views/LoginView.php&name=Login Page&header=Login");
        return "1";
    }

    unset($_SESSION['userDetails']);
    unset($_SESSION['USER_NAME']);
    unset($_SESSION['ID']);
    // handling redirection and alert in client side for the animated alert to display.
    return "0";
}

function OnLoginRequestReceived()
{
    $_POST['email'] = $_POST['postData'][0]['value'];
    $_POST['password'] = $_POST['postData'][1]['value'];
    $validationReturnCode = ValidateLoginForum();

    if ($validationReturnCode != 0) {
        return $validationReturnCode;
    }

    $Db = new Database;

    if (!$Db->IsExistingUser($_POST["email"])) {
        //Functions::Alert("Entered user account doesnt exist.\nConsider registering yourself!");       
        //Functions::Redirect("../Views/LoginView.php");
        return "2";
    }

    if ($loginResult = $Db->LoginUser($_POST["email"], $_POST["password"], false)) {
        if (isset($loginResult['isError']) && $loginResult['isError']) {
            //Functions::Alert("Email and Password doesnt match.");
            //Functions::Redirect("../Views/LoginView.php");
            return "3";
        }

        $_SESSION['userDetails'] = serialize($loginResult['resultObj']);
        $_SESSION['ID'] = $loginResult['resultObj']->Id;
        $_SESSION['USER_NAME'] = $loginResult['resultObj']->UserName;
        //Functions::Redirect("../Views/RedirectView.php?path=../Views/HomeView.php&name=Home Page&header=Home");
        return "0";
    }
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "-1";
    exit();
}

switch ($_POST['requestType']) {
    case "logout":
        echo OnLogoutRequestReceived();
        break;
    case "login":
        echo OnLoginRequestReceived();
        break;
}
?>