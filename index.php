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
<link rel="stylesheet" href="includes/css/index-style.css" />
<link rel="stylesheet" href="includes/css/hover-bttn.css" />
<?php include_once $_SERVER['ZHOST_ROOT'] . '/Common/Header.php'; ?>

<script type="text/javascript">
	function onHomeLinkClicked() {
		let timerInterval;
		Swal.fire({
			title: "Please wait...",
			html: "Redirecting you to Dashboard in <b></b> milliseconds.",
			timer: 2000,
			timerProgressBar: true,
			willOpen: () => {
				Swal.showLoading();
				timerInterval = setInterval(() => {
					const content = Swal.getContent();
					if (content) {
						const b = content.querySelector("b");
						if (b) {
							b.textContent = Swal.getTimerLeft();
						}
					}
				}, 100);
			},
			onClose: () => {
				clearInterval(timerInterval);
			},
		}).then((result) => {
			if (result.dismiss === Swal.DismissReason.timer) {
				window.location = "Views/DashboardView.php";
				return;
			}
		});
	}
</script>

<body>
	<div id="testNav">
		<nav class="links" style="--items: 5;">			
			<a href="javascript: window.location.reload();">Index</a>
			<a href="Views/LoginView.php?refer=index">Sign In</a>
			<a href="Views/RegisterView.php?refer=index">Sign Up</a>
			<a href="Views/AboutUsView.php?refer=index">About Us</a>
			<a href="Views/ContactUsView.php?refer=index">Contact Us</a>
			<span class="line"></span>
		</nav>
	</div>

	<div id="bodyContainer" class="is-preload body">
		<header id="header">
			<h1>zHOST</h1>
			<p>An online mail server system developed as a part of <br />
				college <span style="font-weight: bold;">mini project.</span></p>
		</header>
	</div>
	<!--
	<form method="post" action="Controllers/IndexController.php">
		<input type="submit" name="loginBttn" class="btn btn-primary" value="LOGIN" />
		<input type="submit" name="registerBttn" class="btn btn-primary" value="REGISTER" />
	</form>
	-->

	<footer id="footer">
		<ul class="icons">
			<li><a href="#" class="icon brands fa-github"><span class="label">GitHub</span></a></li>
		</ul>
		<ul class="copyright">
			<?php
			if (IsUserLoggedIn()) {
				$User = unserialize($_SESSION["userDetails"]);
				echo '<li>Logged in as <span style="font-weight: bold;"><a href="javascript:onHomeLinkClicked();">' . $User->UserName . '</a></span>' . ($User->IsAdmin ? " (Admin) " : " (User) ") . '</li>';
			}
			?>
			<li>&copy; zHost <?php echo date("Y"); ?></li>
		</ul>

	</footer>

	<script src="includes/js/IndexViewScript.js"></script>
	<script src="includes/js/jquery.min.js"></script>
</body>

</html>