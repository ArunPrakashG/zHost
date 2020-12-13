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

$_SESSION['PageTitle'] = "Edit Profile";

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
$AccountCreationDate = date("d/m/y g:i A", $time);
$User->UserName = $User->FirstName . " " . $User->LastName;
?>

<html>
<header>    
    <?php include_once $_SERVER['ZHOST_ROOT'] . '/Common/Header.php'; ?>
    <link href="../includes/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="../includes/css/EditProfile.css" rel="stylesheet">
    <script src="../includes/js/EditProfileViewScript.js"></script>
    <script src="../includes/js/jquery.min.js"></script>
    <script src="../includes/js/bootstrap.bundle.min.js"></script>
</header>

<body>
    <div class="container">
        <h1>Edit Your Profile</h1>
        <hr>
        <form class="form-horizontal" method="POST" name="editForm" id="editForm" action="javascript:editRequested();">
            <div class="row">
                <!-- left column -->
                <div class="col-md-3">

                    <div class="text-center">
                        <img src="<?php echo $User->AvatarPath ?>" style="height: 160px; width: 160px;" class="avatar img-circle" alt="avatar">
                        <h6>Change profile picture</h6>

                        <input type="file" name='profilePicture' class="form-control">
                    </div>
                </div>

                <!-- edit form column -->
                <div class="col-md-9 personal-info">
                    <div class='alert alert-info alert-dismissable hidden'>
                        <?php
                        /*
                        if (isset($_SESSION['updateResult']) && $_SESSION['updateStatus'] != 0) {

                            echo "<a class='panel-close close' data-dismiss='alert'>×</a>";
                            echo '<i class="fa fa-coffee"></i>' . $_SESSION['updateResult'];
                            unset($_SESSION['updateResult']);
                        }
                        */
                        ?>
                    </div>
                    <h3>Personal info</h3>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">First name</label>
                        <div class="col-lg-8">
                            <input class="form-control" type="text" required name="firstName" placeholder="Your First Name" value="<?php echo $User->FirstName ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Last name</label>
                        <div class="col-lg-8">
                            <input class='form-control' type='text' required name='lastName' placeholder="Your Last Name" value="<?php echo $User->LastName ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Bio</label>
                        <div class="col-lg-8">
                            <input class="form-control" type="text" name="bio" placeholder="Your Personal Bio" value="<?php echo $User->Bio ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Date of birth</label>
                        <div class="col-lg-8">
                            <input class="form-control" type="date" name="dateOfBirth" required placeholder="Your Date Of Birth" value="<?php echo $User->DateOfBirth ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Address</label>
                        <div class="col-lg-8">
                            <input class='form-control' type='text' required name='address' placeholder="Your Address" value="<?php echo $User->Address ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Email</label>
                        <div class="col-lg-8">
                            <input class="form-control" type="text" name="email" readonly value="<?php echo $User->Email ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">Phone Number</label>
                        <div class="col-lg-8">
                            <input class="form-control" type="text" required maxlength="10" name="phoneNumber" placeholder="Your Phone Number" value="<?php echo $User->PhoneNumber ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">Gender</label>
                        <div class="col-lg-8">
                            <div class="ui-select">
                                <select class="form-control" required name="gender">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Password</label>
                        <div class="col-md-8">
                            <input class="form-control" type="password" required placeholder="Your Password" required name='passWord' value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Confirm password</label>
                        <div class="col-md-8">
                            <input class="form-control" type="password" required placeholder="Retype your password" required name='confirmPassword' value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-8">
                            <input type="submit" class="btn btn-primary" name='submitButton' value="Save Changes">
                            <span></span>
                            <input type="reset" class="btn btn-default" name='clearValueButton' value="Cancel" onclick="cancelForm();">
                        </div>
                    </div>
        </form>
    </div>
    </div>
    </div>
    <hr>
</body>

</html>