<?php
require_once('../zHost/config.php');
require_once('../zHost/includes/connection.php');

if(!isset($_SESSION)){
	session_start();
}

function validateLoginForum(){
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        $_SESSION["loginErrorMessage"] = "Invalid request.";
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

        header("Location: loginView.php");
        return;
    }

    $Db = new Database;
    $Email = secureEscape($_POST["email"]);
    $EncryptedPassword = password_hash(secureEscape($_POST["password"]), PASSWORD_DEFAULT);

    if (!$Db->IsExistingUser($_POST["email"])) {
        $_SESSION["IsError"] = true;
        $_SESSION["loginErrorMessage"] = "Such a user doesn't exist! Please register yourself first.";
        header("Location: loginView.php");
        return;
    }

    if ($Db->LoginUser($Email, $Password, false)) {			
        $_SESSION["IsError"] = false;
        unset($_SESSION["registerErrorMessage"]);        
        header("Location: homeView.php");
        return;
    }
}

?>