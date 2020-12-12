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

error_log(var_dump($_POST));
/*
    array(10) { 
        ["profilePicture"]=> string(0) "" 
        ["firstName"]=> string(10) "First Name" 
        ["lastName"]=> string(9) "Last Name" 
        ["address"]=> string(0) "" 
        ["phoneNumber"]=> string(10) "0000000000" 
        ["gender"]=> string(4) "male" 
        ["userName"]=> string(12) "Arun Prakash" 
        ["passWord"]=> string(0) "" 
        ["confirmPassword"]=> string(0) "" 
        ["submitButton"]=> string(12) "Save Changes" 
    }
*/
