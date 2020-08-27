<?php
require_once $_SERVER['ZHOST_ROOT'] . '/Common/Functions.php';
require_once $_SERVER['ZHOST_ROOT'] . '/Core/UserModel.php';

if (!isset($_SESSION)) {
    session_start();
}

function IsSessionActive($redirectToHomeIfActive, $redirectToLoginIfNotActive)
{
    if (IsUserLoggedIn()) {
        if ($redirectToHomeIfActive) {
            Functions::Alert("You are already logged in! (" . $_SESSION['ID'] . ")\nRedirecting you to Home page...");            
            Functions::Redirect("../Views/RedirectView.php?path=../Views/HomeView.php&name=Home Page&header=Home");
        }

        return true;
    }

    if ($redirectToLoginIfNotActive) {
        Functions::Alert("Logon session expired.\nYou will be redirected to login page now...");  
        Functions::Redirect("../Views/RedirectView.php?path=../Views/LoginView.php&name=Login Page&header=Login");
    }

    return false;
}

function IsUserLoggedIn()
{
    return isset($_SESSION["userDetails"]) && isset($_SESSION["ID"]) && isset($_SESSION["USER_NAME"]);
}

function GetCurrentLoggedInUserName()
{
    if (!IsUserLoggedIn()) {
        return "NA";
    }

    return unserialize($_SESSION["userDetails"])->UserName;
}

function GetCurrentUserEmail(){
    if (!IsUserLoggedIn()) {
        return "NA";
    }

    $details = unserialize($_SESSION["userDetails"]);
    error_log(print_r($details, true));
    return $details->Email;
}
?>