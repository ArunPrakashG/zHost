<?php
session_start();
$_SESSION['PageTitle'] = "Login";
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php include('header.php'); ?>

<body>
	<div class="container">
		<div class="form">
			<form class="login-form" action="login.php" method="post">
				<h2>Login</h2>
				<div class="icons">
					<a href="#"><i class="fab fa-facebook"></i></a>
					<a href="#"><i class="fab fa-google"></i></a>
					<a href="#"><i class="fab fa-twitter"></i></a>
				</div>
				<input type="text" name="username" value="" placeholder="Username" required>
				<input type="password" name="password" value="" placeholder="Password" required>
				<button type="submit" name="button">Login</button>
				<p class="options">Not Registered? <a href="#">Create an Account</a></p>
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

<?php include('footer.php'); ?>

</html>