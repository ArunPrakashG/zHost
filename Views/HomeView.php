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
<link rel="stylesheet" type="text/css" href="../includes/css/home.css" />
<script src="../includes/js/HomeViewScript.js"></script>

<header>
    <?php require_once '../Common/Header.php' ?>

    <div class="header-container">
        <label for="check">
            <i class="fas fa-bars" id="sidebar_btn"></i>
        </label>
        <div class="left_area">
            <h3>z<span>Host</span></h3>
        </div>
        <div class="right_area">
            <a onclick="logoutRequested();" href="javascript:void(0);" class="logout_btn">Logout</a>
        </div>
    </div>

</header>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="profile_info">
                <img src=<?php echo $User->AvatarPath ?> class="profile_image" alt="place_holder">
                <h4><?php echo $User->UserName ?></h4>
                <h4><?php echo $User->Email ?></h4>
            </div>
            <hr class="breaker" />
            <div class="dashboard-contents item-container">
                <a href="#" class="item"><span style="font-weight: bold;">Inbox</span></a>
                <a href="#" class="item"><span style="font-weight: bold;">Compose</span></a>
                <a href="#" class="item"><span style="font-weight: bold;">Draft</span></a>
                <a href="#" class="item"><span style="font-weight: bold;">Trash</span></a>
                <a href="#" class="item"><span style="font-weight: bold;">Settings</span></a>
            </div>
        </div>

        <div class="sel-body">
            <table class="styled-table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Sender</th>
                        <th>Subject</th>
                        <th>Received Time</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                    ?>
                    <tr>
                        <td class="table-row-field">1</td>
                        <td class="table-row-field">Jobin</td>
                        <td class="table-row-field">ghfghhgfhgfghasdasd</td>
                        <td class="table-row-field">235615</td>
                        <td class="table-row-field">
                            <a class="deletebttn">test</a>
                        </td>
                    </tr>
                    <tr class="active-row">
                        <td class="table-row-field">1</td>
                        <td class="table-row-field">Shijo</td>
                        <td class="table-row-field">asdasdasdadasdasd</td>
                        <td class="table-row-field">235615</td>
                        <td class="table-row-field">
                            <a class="deletebttn">test</a>
                        </td>
                    </tr>
                    <!-- and so on... -->
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>