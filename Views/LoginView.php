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

<script type="text/javascript">
    function loginRequested() {
        console.log("login request received");
        var data = $('.login-form').serializeArray();
        console.log(data);
        $.ajax({
            method: "POST",
            url: "../Controllers/UserController.php",
            data: {
                requestType: 'login',
                postData: data
            },
            success: function(result) {
                console.log(result);                
                switch (result) {
                    case "-1":
                        console.log("Invalid request type.");
                        return;
                    case "0":
                        // success                        
                        swal("Welcome to zHost!", "You will be redirected to your inbox.", "success").then((value) => {
                            document.location = "../Views/RedirectView.php?path=../Views/HomeView.php&name=Home Page&header=Home";
                        });
                        break;
                    case "2":
                        // account doesnt exist
                        swal("Account doesn't exist!", "Consider registering yourself.", "warning").then((value) => {
                            document.location = "../Views/LoginView.php";
                        });
                        break;
                    case "3":
                        // email and pass doesnt match                        
                        swal("Email/Password missmatch.", "Entered email and password doesn't match.", "warning").then((value) => {
                            document.location = "../Views/LoginView.php";
                        });
                        break;
                    case "10":
                        // email invalid                        
                        swal("Entered email is empty/invalid.", "All email ids should have @zhost.com appended at end.", "warning").then((value) => {
                            document.location = "../Views/LoginView.php";
                        })
                        break;
                    case "11":
                        // password invalid
                        swal("Entered password is empty/invalid.", "Passwords should not contain whitespaces or empty charecters (ASCII included)", "warning").then((value) => {
                            document.location = "../Views/LoginView.php";
                        })
                        break;
                }
            }
        });
    }
</script>

<link rel="stylesheet" href="../includes/css/login-style.css">

<body>
    <div class="container">
        <div class="form">
            <form class="login-form" method="post" action="javascript:loginRequested();">
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
                <p class="options">Not Registered ? <a href="../Views/RegisterView.php">Register here</a>!</p>
            </form>
        </div>
    </div>

</body>

<?php require_once '../Common/Footer.php'; ?>

</html>