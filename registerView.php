<?php require_once('../zHost/config.php') ?>
<?php require_once('../zHost/includes/connection.php') ?>

<?php
session_start();
$_SESSION['PageTitle'] = "Register";
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<link rel="stylesheet" href="includes/css/hover-bttn.css" />

<head>
	<style>
		* {
			box-sizing: border-box;
			font-family: "Roboto", sans-serif;
		}

		.headTitle {
			color: #f1f1f1;
		}

		/* Add padding to containers */
		.container {
			padding: 16px;
		}

		/* Full-width input fields */
		input[type=text],
		input[type=password] {
			width: 100%;
			padding: 15px;
			margin: 5px 0 22px 0;
			display: inline-block;
			border: none;
			background: #f1f1f1;
		}

		input[type=text]:focus,
		input[type=password]:focus {
			background-color: #ddd;
			outline: none;
		}

		/* Overwrite default styles of hr */
		hr {
			border: 1px solid #f1f1f1;
			margin-bottom: 25px;
		}

		/* Set a style for the submit/register button */
		.registerbtn {
			background-color: #4CAF50;
			color: white;
			padding: 16px 20px;
			margin: 8px 0;
			border: none;
			cursor: pointer;
			width: 100%;
			opacity: 0.9;
		}

		.registerbtn:hover {
			opacity: 1;
		}

		/* Add a blue text color to links */
		a {
			color: dodgerblue;
		}

		/* Set a grey background color and center the text of the "sign in" section */
		.signin {
			background-color: transparent;
			text-align: center;
		}
	</style>

	<script type="text/javascript">

	</script>
</head>

<body>
	<div class="main">
		<form method="POST">
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

				<p class="headTitle">By creating an account you agree to our <a href="#">Terms & Conditions</a>.</p>
				<button type="submit" class="registerbtn">Register</button>
			</div>

			<div class="container signin">
				<p class="headTitle">Already have an account ? <a href="loginView.php">Log in</a>!</p>
			</div>
		</form>

		<?php if (isset($_SESSION["registerErrorMessage"])) { ?>		
			<p><?php echo $_SESSION["registerErrorMessage"] ?></p>
		<?php }?>
		
	</div>

	<?php
	function validateRegisterForum()
	{
		if ($_SERVER["REQUEST_METHOD"] != "POST") {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"] = "Submition failed internally.";
			return false;
		}

		if (!isset($_POST["email"])) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"]  = "Email cannot be empty!";
			return false;
		}

		if (!isset($_POST["username"])) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"]  = "Username cannot be empty!";
			return false;
		}

		if (!isset($_POST["psw"])) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"]  = "Password cannot be empty!";
			return false;
		}

		if (!isset($_POST['psw-repeat'])) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"]  = "Please repeat the password.";
			return false;
		}

		if ($_POST["psw"] != $_POST["psw"]) {
			$_SESSION["registerErrorMessage"] = "Entered password doesn't match.";
			$_SESSION["IsError"] = true;
			return false;
		}

		$_SESSION["IsError"] = false;
		unset($_SESSION["registerErrorMessage"]);
		return true;
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (!validateRegisterForum()) {
			if (!isset($_SESSION["registerErrorMessage"])) {
				$_SESSION["registerErrorMessage"] = "Failed to register. try again.";
			}
	
			header("Location: registerView.php");
			return;
		}

		$Db = new Database;

		if ($Db->IsExistingUser($_POST["email"])) {
			$_SESSION["IsError"] = true;
			$_SESSION["registerErrorMessage"] = "The specified email id already exists. Please choose another one.";
			header("Location: registerView.php");
			return;
		}

		$Password = password_hash($_POST["psw"], PASSWORD_DEFAULT);
		if ($Db->RegisterUser($_POST['username'], $_POST['email'], $Password, false)) {
			$_SESSION["IsError"] = false;
			unset($_SESSION["registerErrorMessage"]);
			$_SESSION["RegistrationMessage"] = "Registration successfull! Please login!";
			header("Location: loginView.php");
			return;
		}
	}
	?>
</body>

<footer>

</footer>

</html>