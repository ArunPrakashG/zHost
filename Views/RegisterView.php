<?php
require_once '../Core/Config.php';
require_once '../Core/SessionCheck.php';

if (Config::DEBUG) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

if (!isset($_SESSION)) {
	session_start();
}

$_SESSION['PageTitle'] = "Register";

if (isset($_GET['refer']) && strcmp($_GET['refer'], "index")) {
	unset($_SESSION["userDetails"]);
	unset($_SESSION["ID"]);
} else {
	if (IsSessionActive(true, false)) {
		exit();
	}
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php require_once '../Common/Header.php' ?>
<link rel="stylesheet" href="../includes/css/register-view.css" />
<link rel="stylesheet" href="../includes/css/scroll-bttn.css" />
<script src="../includes/js/RegisterViewScript.js"></script>

<body>
	<div class="container">
		<form id="reg-form" class="login-form" method="POST" action="javascript:registerRequested();" enctype="multipart/form-data">
			<div>
				<h1 class="title">Register</h1>
				<p class="subheading">Please fill in this form to create an account.</p>
				<hr>

				<label class="heading" for="email"><b>Email</b></label>
				<input type="text" placeholder="Enter Email" name="email" id="email" required value=<?php echo isset($_SESSION['form-data']['email']) ? $_SESSION['form-data']['email'] : "" ?>>

				<label class="heading" for="username"><b>Username</b></label>
				<input type="text" placeholder="Enter Username" name="username" id="username" required value=<?php echo isset($_SESSION['form-data']['username']) ? $_SESSION['form-data']['username'] : "" ?>>

				<label class="heading" for="psw headTitle"><b>Password</b></label>
				<input type="password" placeholder="Enter Password" name="psw" id="psw" maxlength="80" required value=<?php echo isset($_SESSION['form-data']['psw']) ? $_SESSION['form-data']['psw'] : "" ?>>

				<label class="heading" for="psw-repeat headTitle"><b>Repeat Password</b></label>
				<input type="password" placeholder="Repeat Password" name="psw-repeat" maxlength="80" required id="psw-repeat" value=<?php echo isset($_SESSION['form-data']['psw-repeat']) ? $_SESSION['form-data']['psw-repeat'] : "" ?>>

				<label class="heading" for="p-number"><b>Phone Number <span style="color:grey">(without code)</span></b></label>
				<input type="text" placeholder="Enter Phone Number" name="pnumber" maxlength="10" required id="p-number" value=<?php echo isset($_SESSION['form-data']['pnumber']) ? $_SESSION['form-data']['pnumber'] : "" ?> >

				<label class="heading" for="sec-quest"><b>Security Question</b></label>
				<input type="text" placeholder="Enter Security Question" name="secquest" id="sec-quest" value=<?php echo isset($_SESSION['form-data']['secquest']) ? $_SESSION['form-data']['secquest'] : "" ?>>
				<input type="text" style="margin: 0px" placeholder="Answer" name="secans" id="sec-ans" value=<?php echo isset($_SESSION['form-data']['secans']) ? $_SESSION['form-data']['secans'] : "" ?>>
				
				<label class="heading" for="pro-image"><b>Profile Image <span style="color:grey">(Default if empty)</span></b></label>
				<input type="file" placeholder="Select and Upload" name="profileimage" id="pro-image">

				<p class="headTitle">By registering you agree to our <a href="#">Terms & Conditions</a>.</p>
				<hr>

				<button type="submit" class="scroll-bttn">Register</button>
			</div>

			<div class="container signin">
				<p>Already have an account ? <a href="../Views/LoginView.php">Log in</a>!</p>
			</div>
	</div>
	</form>

	</div>

</body>

<?php //include_once '../Common/Footer.php'; 
?>

</html>