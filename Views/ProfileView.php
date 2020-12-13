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

$_SESSION['PageTitle'] = "Your Profile";

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
    Functions::Redirect("../Views/LoginView.php");
    exit();
}

$User = unserialize($_SESSION["userDetails"]);
$time = strtotime($User->DateCreated);
$User->UserName = $User->FirstName . " " . $User->LastName;
$AccountCreationDate = date("d/m/y g:i A", $time);

if (isset($_SESSION['updateStatus']) && $_SESSION['updateStatus'] == 0) {
    unset($_SESSION['updateResult']);
    unset($_SESSION['updateStatus']);

    $Db = new Database();
    $loginResult = $Db->LoginUser($User->Email, $User->Password, false);
    if ($loginResult['isError']) {
        unset($_SESSION["userDetails"]);

        if (!IsUserLoggedIn()) {
            Functions::Alert("Session expired!\nYou will be required to login again.");
            Functions::Redirect("../Views/LoginView.php");
            exit();
        }
    }
}

?>

<html>
<header>
    <?php include_once $_SERVER['ZHOST_ROOT'] . '/Common/Header.php'; ?>
    <link href="../includes/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="../includes/css/ProfileView.css" rel="stylesheet" id="bootstrap-css">
    <script src="../includes/js/jquery.min.js"></script>
    <script src="../includes/js/bootstrap.bundle.min.js"></script>
</header>

<body>
    <div class="container emp-profile">
        <form method="post">
            <div class="row">
                <div class="col-md-4">
                    <div class="profile-img">
                        <img src="<?php echo $User->AvatarPath ?>" alt="" />
                        <!--
                        <div class="file btn btn-lg btn-primary">
                            Change Photo
                            <input type="file" name="file" />                           
                        </div>
                        -->
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="profile-head">
                        <h5>
                            <?php echo $User->UserName ?>
                        </h5>
                        <h6>
                            <?php echo $User->Bio ?>
                        </h6>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">About</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-2">
                    <a type="submit" class="profile-edit-btn" name="btnAddMore" value="Edit Profile" href="../Views/EditProfileView.php">Edit Profile</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="profile-work">
                        <p>WORK LINK</p>
                        <a href=""><?php echo $User->WorkLink ?></a><br />
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="tab-content profile-tab" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <!--
                            <div class="row">
                                <div class="col-md-6">
                                    <label>User Id</label>
                                </div>
                                <div class="col-md-6">
                                    <p><?php echo $User->Id ?></p>
                                </div>
                            </div>
                            -->
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Name</label>
                                </div>
                                <div class="col-md-6">
                                    <p><?php echo $User->UserName ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Email</label>
                                </div>
                                <div class="col-md-6">
                                    <p><?php echo $User->Email ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Date of Birth</label>
                                </div>
                                <div class="col-md-6">
                                    <p><?php echo $User->DateOfBirth ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Member Since</label>
                                </div>
                                <div class="col-md-6">
                                    <p><?php echo $AccountCreationDate ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Address</label>
                                </div>
                                <div class="col-md-6">
                                    <p><?php echo $User->Address ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Phone</label>
                                </div>
                                <div class="col-md-6">
                                    <p><?php echo $User->PhoneNumber <= 0 ? "-NA-" : $User->PhoneNumber ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Profession</label>
                                </div>
                                <div class="col-md-6">
                                    <p><?php echo $User->Profession ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>

</html>