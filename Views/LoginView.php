<?php
require_once '../Core/Config.php';
require_once '../Common/Functions.php';

if (Config::DEBUG) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

if (!isset($_SESSION)) {
	session_start();
}

$_SESSION['PageTitle'] = "Login";
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php require_once '../Common/Header.php'; ?>

<link rel="stylesheet" href="../includes/css/login-style.css">
<link rel="stylesheet" href="../includes/css/model-box.css">
<script src="../includes/js/model-box.js"></script>

<div id="alert-model" class="modal">
	<div class="modal-content">
		<span class="close">&times;</span>
		<p class="modal-text-content">error message placeholder</p>
	</div>
</div>

<body>
	<div class="container">
		<div class="form">
			<form class="login-form" action="../Controllers/LoginController.php" method="post">
				<h2>Login</h2>
				<div class="icons">
					<a href="#"><i class="fab fa-facebook"></i></a>
					<a href="#"><i class="fab fa-google"></i></a>
					<a href="#"><i class="fab fa-twitter"></i></a>
				</div>
				<input type="text" name="email" value="" placeholder="Email" required>
				<input type="password" name="password" value="" placeholder="Password" required>
				<button type="submit" name="button">Login</button>
				<p class="options">Not Registered? <a href="../Views/RegisterView.php">Register here</a>!</p>
			</form>

			<?php

			if (isset($_SESSION["loginErrorMessage"]) || (isset($_SESSION["IsError"]) && $_SESSION["IsError"])) {
				if (isset($_GET['errorCode'])) {
					switch ($_GET['errorCode']) {
						case 1:
							// forum validation failure
							echo '<script type="text/javascript">showAlertWindow(' . isset($_SESSION["loginErrorMessage"]) ? $_SESSION["loginErrorMessage"] : "Forum validation failed." . ')</script>';
							//echo '<p class="login-error-msg">⚠️ ' . isset($_SESSION["loginErrorMessage"]) ? $_SESSION["loginErrorMessage"] : "Forum validation failed." . '</p>';
							break;
						case 2:
							// user doesnt exist | should register
							echo '<script type="text/javascript">showAlertWindow(' . isset($_SESSION["loginErrorMessage"]) ? $_SESSION["loginErrorMessage"] : "Such a user doesn't exist." . ')</script>';
							//echo '<p class="login-error-msg">⚠️ ' . isset($_SESSION["loginErrorMessage"]) ? $_SESSION["loginErrorMessage"] : "Such a user doesnt exist." . '</p>';
							break;
						case 3:
							// invalid details
							echo '<script type="text/javascript">showAlertWindow(' . isset($_SESSION["loginErrorMessage"]) ? $_SESSION["loginErrorMessage"] : "Invalid details." . ')</script>';
							//echo '<p class="login-error-msg">⚠️ ' . isset($_SESSION["loginErrorMessage"]) ? $_SESSION["loginErrorMessage"] : "Invalid details." . '</p>';
							break;
						default:
							echo '<script type="text/javascript">showAlertWindow(' . isset($_SESSION["loginErrorMessage"]) ? $_SESSION["loginErrorMessage"] : "Unknown error code." . ')</script>';
							//echo '<p class="login-error-msg">⚠️ ' . isset($_SESSION["loginErrorMessage"]) ? $_SESSION["loginErrorMessage"] : "Unknown error code." . '</p>';
							break;
					}

					return;
				}

				// no error code
				// not aware of what happened
				echo '<script type="text/javascript">showAlertWindow(' . isset($_SESSION["loginErrorMessage"]) ? $_SESSION["loginErrorMessage"] : "Login failed (Unknown reason)" . ')</script>';
				//echo '<p class="login-error-msg">⚠️ ' . isset($_SESSION["loginErrorMessage"]) ? $_SESSION["loginErrorMessage"] : "Login failed (Unknown reason)." . '</p>';
				return;
			}
			?>
		</div>
	</div>

</body>

<?php require_once '../Common/Footer.php'; ?>

</html>