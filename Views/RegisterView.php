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
<link rel="stylesheet" href="../includes/css/hover-bttn.css" />
<link rel="stylesheet" href="../includes/css/register-view.css" />

<body>
	<div class="main">
		<form method="POST" action="../Controllers/RegisterController.php">
			<div class="container">
				<h1 class="headTitle">Register</h1>
				<p class="headTitle">Please fill in this form to create an account.</p>
				<hr>

				<label class="headTitle" for="email"><b>Email</b></label>
				<input type="text" placeholder="Enter Email" name="email" id="email" required>

				<label class="headTitle" for="username"><b>Username</b></label>
				<input type="text" placeholder="Enter Username" name="username" id="username" required>

				<label class="headTitle" for="psw headTitle"><b>Password</b></label>
				<input type="password" placeholder="Enter Password" name="psw" id="psw" required>

				<label class="headTitle" for="psw-repeat headTitle"><b>Repeat Password</b></label>
				<input type="password" placeholder="Repeat Password" name="psw-repeat" id="psw-repeat" required>
				<hr>

				<div class="flex-container">
					<div>
						<input type="checkbox" checked="false" name="tocAgreement" value="Agree to Teams & Conditions" required>
						<p class="headTitle">By creating an account you agree to our <a href="#">Terms & Conditions</a>.</p>
					</div>
				</div>

				<button type="submit" class="registerbtn">Register</button>
			</div>

			<div class="container signin">
				<p class="headTitle">Already have an account ? <a href="../Views/LoginView.php">Log in</a>!</p>
			</div>
	</div>
	</form>

	</div>

</body>

<?php include_once '../Common/Footer.php'; ?>

</html>