<?php
session_start();

function validateLoginForum(){
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        $_SESSION["loginErrorMessage"] = "Submition failed internally.";
        return false;
    }

    if (empty($_POST["username"])) {
        $_SESSION["loginErrorMessage"]  = "Username cannot be empty!";
        return false;
    }

    if(empty($_POST["password"])){
        $_SESSION["loginErrorMessage"]  = "Password cannot be empty!";
        return false;
    }

    $Username = secureEscape($_POST["username"]);
    $Username = secureEscape($_POST["password"]);
    $_SESSION["Username"] = $Username;
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

if (!validateLoginForum()) {
    if(!isset($_SESSION["loginErrorMessage"])){
        $_SESSION["loginErrorMessage"] = "Submition failed.";        
    }
    
    header("Location: loginView.php");
    exit;
}

header("Location: home.php");
?>