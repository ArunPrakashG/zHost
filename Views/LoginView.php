<?php
require_once '../Core/Config.php';

if(Config::DEBUG){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if(!isset($_SESSION)){
	session_start();
}

$_SESSION['PageTitle'] = "Login";
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php require_once '../Common/Header.php'; ?>

<link rel="stylesheet" href="../includes/css/login-style.css">
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
				<p class="options">Not Registered? <a href="RegisterView.php">Register here</a>!</p>
			</form>

			<?php if (isset($_SESSION["RegistrationMessage"]) && isset($_SESSION["IsError"]) && $_SESSION["IsError"]){ ?>
				<p class="register-success-msg">You are registered successfully! Login to continue.</p>
			<?php } ?>

			<?php if (isset($_SESSION["loginErrorMessage"])) { ?>
				<p class="login-error-msg">⚠️ <?php echo $_SESSION["loginErrorMessage"] ?></p>
			<?php } ?>

		</div>
	</div>

</body>

<?php require_once '../Common/Footer.php'; ?>

</html>