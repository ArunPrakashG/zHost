<?php

require_once $_SERVER['ZHOST_ROOT'] . '/Core/SessionCheck.php';
require_once $_SERVER['ZHOST_ROOT'] . '/Core/UserModel.php';

if (!isset($_SESSION)) {
	session_start();
}

$_SESSION['PageTitle'] = "Welcome!";
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<link rel="stylesheet" href="includes/css/index.css" />
<link rel="stylesheet" href="includes/css/hover-bttn.css" />
<script src="includes/js/IndexViewScript.js"></script>

<?php include_once $_SERVER['ZHOST_ROOT'] . '/Common/Header.php'; ?>

<body class="is-preload body">
	<header id="header">
		<h1>z<span class="host-span">Host</span></h1>
		<p>An online mail server system developed as a part of <br />
			college <span style="font-weight: bold;">mini project.</span></p>
	</header>

	<form method="post" action="Controllers/IndexController.php">
		<input type="submit" name="loginBttn" class="btn btn-primary" value="LOGIN" />
		<input type="submit" name="registerBttn" class="btn btn-primary" value="REGISTER" />
	</form>

	<footer id="footer">
		<ul class="icons">
			<li><a href="#" class="icon brands fa-github"><span class="label">GitHub</span></a></li>
		</ul>
		<ul class="copyright">
			<?php
			if (IsUserLoggedIn()) {
				$User = unserialize($_SESSION["userDetails"]);
				echo '<li>Logged in as <span style="font-weight: bold;"><a href="onHomeLinkClicked();">' . $User->UserName . '</a></span>' . ($User->IsAdmin ? " (Admin) " : " (User) ") . '</li>';
			}
			?>
			<li>&copy; zHost <?php echo date("Y"); ?></li>
		</ul>

	</footer>
</body>

</html>