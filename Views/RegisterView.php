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
		<form class="login-form" method="POST" action="javascript:registerRequested();">
			<div>
				<h1 class="title">Register</h1>
				<p class="subheading">Please fill in this form to create an account.</p>
				<hr>

				<label class="heading" for="email"><b>Email</b></label>
				<input type="text" placeholder="Enter Email" name="email" id="email" required>

				<label class="heading" for="username"><b>Username</b></label>
				<input type="text" placeholder="Enter Username" name="username" id="username" required>

				<label class="heading" for="psw headTitle"><b>Password</b></label>
				<input type="password" placeholder="Enter Password" name="psw" id="psw" required>

				<label class="heading" for="psw-repeat headTitle"><b>Repeat Password</b></label>
				<input type="password" placeholder="Repeat Password" name="psw-repeat" id="psw-repeat" required>

				<label class="heading" for="psw-repeat"><b>Phone Number <span style="color:grey">(without code)</span></b></label>
				<input type="text" placeholder="Enter Phone Number" name="pnumber" id="p-number" required>

				<label class="heading" for="sec-quest"><b>Security Question</b></label>
				<input type="text" placeholder="Enter Security Question" name="secquest" id="sec-quest">
				<input type="text" style="margin: 0px" placeholder="Answer" name="secans" id="sec-ans">

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