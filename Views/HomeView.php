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

$_SESSION['PageTitle'] = "Home";

// these variables are no longer required or checked at
unset($_SESSION["loginErrorMessage"]);
unset($_SESSION["IsError"]);
unset($_SESSION["registerErrorMessage"]);
unset($_SESSION["IsError"]);
unset($_SESSION["RegistrationMessage"]);
unset($_SESSION["rq"]);

// check for user logged in because
// if user tried to directly access this page via url, he should be redirected back to login and terminate this view
$User;

if (!IsUserLoggedIn()) {
    Functions::Alert("Session expired!\nYou will be required to login again.");
    Functions::Redirect("../Views/LoginRedirectView.php");
    exit();
}

$User = unserialize($_SESSION["userDetails"]);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<link rel="stylesheet" type="text/css" href="../includes/css/dashboard-style.css" />
<script src="../includes/js/HomeViewScript.js"></script>

<header>
    <?php require_once '../Common/Header.php' ?>

    <label for="check">
        <i class="fas fa-bars" id="sidebar_btn"></i>
    </label>
    <div class="left_area">
        <h3>z<span>Host</span></h3>
    </div>
    <div class="right_area">
        <a onclick="logoutRequested();" href="javascript:void(0);" class="logout_btn">Logout</a>
    </div>
</header>

<body>
    <div class="sidebar">
        <div class="profile_info">
            <img src="" class="profile_image" alt="place_holder">
            <h4><?php echo $User->UserName ?></h4>
            <h6><?php echo $User->MailID ?></h6>
        </div>
        <div class="dashboard-contents">
            <a href="#" class="sidebar-item"><i class="fas fa-desktop"></i><span>Dashboard</span></a>
            <a href="#" class="sidebar-item"><i class="fas fa-cogs"></i><span>Components</span></a>
            <a href="#" class="sidebar-item"><i class="fas fa-table"></i><span>Tables</span></a>
            <a href="#" class="sidebar-item"><i class="fas fa-th"></i><span>Forms</span></a>
            <a href="#" class="sidebar-item"><i class="fas fa-info-circle"></i><span>About</span></a>
            <a href="#" class="sidebar-item"><i class="fas fa-sliders-h"></i><span>Settings</span></a>
        </div>
    </div>
</body>

<?php include_once('../Common/Footer.php') ?>

</html>