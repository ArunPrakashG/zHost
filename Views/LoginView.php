<?php
require_once '../Core/Config.php';
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

$_SESSION['PageTitle'] = "Login";

if (isset($_GET['refer']) && strcmp($_GET['refer'], "index")) {
    unset($_SESSION['userDetails']);
    unset($_SESSION['ID']);
} else {
    if (IsSessionActive(true, false)) {
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php require_once '../Common/Header.php'; ?>

<script type="text/javascript" src="../includes/js/LoginViewScript.js"></script>
<link rel="stylesheet" href="../includes/css/login-style.css">

<body>
    <div class="container">
        <div class="form">
            <form id="login-form" class="login-form" method="post" action="javascript:loginRequested();">
                <h2>zHost Login</h2>
                <div class="icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-google"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
                <input type="text" name="email" value="" placeholder="Email" required>
                <input type="password" name="password" value="" placeholder="Password" required>
                <button type="submit" name="button">Login</button>
                <br />
                <p class="options">Forgot Password ? <a href="javascript:onForgotPasswordClicked();">Click Here</a>!</p>
                <p class="options">Not Registered ? <a href="../Views/RegisterView.php">Register here</a>!</p>
                <p class="options">Wanted Home page ? <a href="../Index.php">Home page</a>!</p>
            </form>
        </div>
    </div>

</body>

<?php require_once '../Common/Footer.php'; ?>

</html>