<?php
define('ROOT_PATH', realpath(dirname(__FILE__)));

if(!isset($_SESSION)){
	session_start();
}

$_SESSION['PageTitle'] = "Index";
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<link rel="stylesheet" href="includes/css/index.css" />
<link rel="stylesheet" href="includes/css/hover-bttn.css" />

<body class="is-preload">
	<header id="header">
		<h1>zHost</h1>
		<p>An online mail server system developed as a part of <br />
			college <span style="font-weight: bold;">mini project.</span></p>
	</header>

	<form method="post">
		<input type="submit" name="loginBttn" class="btn btn-primary" value="LOGIN" />
		<input type="submit" name="registerBttn" class="btn btn-primary" value="REGISTER" />
	</form>

	<?php
	if (array_key_exists('loginBttn', $_POST)) {
		$_SESSION['rq'] = "login";
		header("Location: loginView.php");
		return;
	}

	if (array_key_exists('registerBttn', $_POST)) {
		$_SESSION['rq'] = "register";
		header("Location: registerView.php");
		return;
	}
	?>

	<footer id="footer">
		<ul class="icons">
			<li><a href="#" class="icon brands fa-twitter"><span class="label">Twitter</span></a></li>
			<li><a href="#" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>
			<li><a href="#" class="icon brands fa-github"><span class="label">GitHub</span></a></li>
			<li><a href="#" class="icon fa-envelope"><span class="label">Email</span></a></li>
		</ul>
		<ul class="copyright">
			<li>&copy; zHost <?php echo date("Y"); ?></li>
		</ul>

	</footer>

	<!-- Scripts -->
	<script src="includes/js/main.js"></script>

</body>

</html>