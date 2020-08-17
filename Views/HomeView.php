<?php
require_once '../Core/Config.php';
require_once '../Core/UserModel.php';

if (Config::DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (!isset($_SESSION)) {
    session_start();
}

$_SESSION['PageTitle'] = "Home";

unset($_SESSION["loginErrorMessage"]);
unset($_SESSION["IsError"]);
unset($_SESSION["registerErrorMessage"]);
unset($_SESSION["IsError"]);
unset($_SESSION["RegistrationMessage"]);
unset($_SESSION["rq"]);

$User = new LoggedInUserResult(false, false);
if (!isset($_SESSION['userDetails'])) {
    $User = NULL;
    return;
}

$User = $_SESSION["userDetails"];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<link rel="stylesheet" type="text/css" href="../includes/css/dashboard-style.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" charset="utf-8"></script>
<link rel="stylesheet" href="../includes/css/model-box.css">
<script src="../includes/js/model-box.js"></script>

<div id="alert-model" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p class="modal-text-content">error message placeholder</p>
    </div>
</div>

<header>
    <label for="check">
        <i class="fas fa-bars" id="sidebar_btn"></i>
    </label>
    <div class="left_area">
        <h3>z<span>Host</span></h3>
    </div>
    <div class="right_area">
        <a href="#" class="logout_btn">Logout</a>
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

    <script type="text/javascript">
        $(document).ready(function() {
            $('.nav_btn').click(function() {
                $('.mobile_nav_items').toggleClass('active');
            });
        });
    </script>
</body>

<?php include_once('../Common/Footer.php')
?>

</html>