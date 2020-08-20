<?php
require_once '../Core/Config.php';
require_once '../Core/UserModel.php';
require_once '../Common/Functions.php';
require_once '../Core/SessionCheck.php';

if (Config::DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (!isset($_SESSION)) {
    session_start();
}

function Logout()
{
    if (!IsUserLoggedIn()) {
        Functions::Alert("You have not logged in yet.");
        return 1;
    }

    unset($_SESSION['userDetails']);
    unset($_SESSION['USER_NAME']);
    unset($_SESSION['ID']);
    // handling redirection and alert in client side for the animated alert to display.
    return 0;
}

// since its ajax, we should 'print' the return value as its an http request and there is no concept of datatype in these requests
// only plain raw string formate, so return as string of the respective type and parse on client side
switch ($_POST['requestType']) {
    case "logout":
        echo Logout();
        break;
    default:
        echo 2;
        break;
}
?>