<?php
require_once '../Common/Functions.php';
require_once '../Core/Config.php';
require_once '../Core/DatabaseManager.php';

if(Config::DEBUG){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if(!isset($_SESSION)){
	session_start();
}

function validateLoginForum(){
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        $_SESSION["loginErrorMessage"] = "Invalid request type.";
        return false;
    }

    if (empty($_POST["email"])) {
        $_SESSION["loginErrorMessage"]  = "Username cannot be empty!";
        return false;
    }

    if(empty($_POST["password"])){
        $_SESSION["loginErrorMessage"]  = "Password cannot be empty!";
        return false;
    }
    
    return true;
}

function secureEscape($data) {
    if(empty($data)){
        return "";
    }

    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if (!validateLoginForum()) {
        if (!isset($_SESSION["loginErrorMessage"])) {
            $_SESSION["loginErrorMessage"] = "Failed to login. try again.";				
        }

        Functions::Redirect("../Views/LoginView.php?errorCode=1");
        exit();
    }

    $Db = new Database;
    $Email = secureEscape($_POST["email"]);

    if (!$Db->IsExistingUser($_POST["email"])) {
        $_SESSION["IsError"] = true;
        $_SESSION["loginErrorMessage"] = "Such a user doesn't exist! Please register yourself first.";
        Functions::Redirect("../Views/LoginView.php?errorCode=2");
        exit();
    }

    if ($loginResult = $Db->LoginUser($Email, secureEscape($_POST["password"]), false)) {
        if(isset($loginResult['isError']) && $loginResult['isError']){
            $_SESSION["IsError"] = $loginResult["isError"];       
            $_SESSION["loginErrorMessage"] = isset($loginResult["errorMessage"]) ? $loginResult["errorMessage"] : "Error: Login failed";
            Functions::Redirect("../Views/LoginView.php?errorCode=3");
        }
        
        //throw new Exception(print_r($loginResult));
        $_SESSION['userDetails'] = $loginResult['resultObj'];
        $_SESSION['ID'] = $loginResult['resultObj']->Id;
        Functions::Redirect("../Views/HomeView.php");
        exit();
    }
}

?>