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
<link rel="stylesheet" href="../includes/css/bootstrap.min.css" />
<script src="../includes/js/RegisterViewScript.js"></script>
<script src="../includes/js/bootstrap.bundle.min.js"></script>

<body class="imageBg">
	<div class="container">
		<form id="reg-form" class="login-form" method="POST" action="javascript:registerRequested();" enctype="multipart/form-data">
			<div>
				<h1 class="title">Register</h1>
				<center>
					<p class="subheading">Please fill this form to create an account.</p>
				</center>

				<hr>

				<div class="mb-3">
					<label class="form-label" for="email"><b>zHost User ID</b></label>
					<input type="text" class="form-control" placeholder="Enter Email" name="email" id="email" required value=<?php echo isset($_SESSION['form-data']['email']) ? $_SESSION['form-data']['email'] : "" ?>>
				</div>

				<div class="mb-3">
					<label class="form-label" for="firstName"><b>First Name</b></label>
					<input type="text" class="form-control" placeholder="Enter your First Name" name="firstName" id="firstName" required value=<?php echo isset($_SESSION['form-data']['firstName']) ? $_SESSION['form-data']['firstName'] : "" ?>>
				</div>

				<div class="mb-3">
					<label class="form-label" for="lastName"><b>Last Name</b></label>
					<input type="text" class="form-control" placeholder="Enter your Last Name" name="lastName" id="lastName" required value=<?php echo isset($_SESSION['form-data']['lastName']) ? $_SESSION['form-data']['lastName'] : "" ?>>
				</div>

				<div class="mb-3">
					<label class="form-label" for="address"><b>Address</b></label>
					<input type="text" class="form-control" placeholder="Enter your Address" name="address" id="address" required value=<?php echo isset($_SESSION['form-data']['address']) ? $_SESSION['form-data']['address'] : "" ?>>
				</div>

				<div class="mb-3">
					<label class="form-label" for="dateOfBirth"><b>Date Of Birth</b></label>
					<input type="Date" class="form-control" placeholder="Enter your Date of Birth" name="dateOfBirth" id="dateOfBirth" required value=<?php echo isset($_SESSION['form-data']['dateOfBirth']) ? $_SESSION['form-data']['dateOfBirth'] : "" ?>>
				</div>

				<div class="mb-3">
					<label class="form-label" for="gender"><b>Gender</b></label>

					<select class="form-control" required name="gender">
						<option value="male">Male</option>
						<option value="female">Female</option>
						<option value="other">Other</option>
					</select>
				</div>

				<div class="mb-3">
					<label class="form-label" for="psw headTitle"><b>Password</b></label>
					<input type="password" class="form-control" placeholder="Enter Password" name="psw" id="psw" maxlength="80" required value=<?php echo isset($_SESSION['form-data']['psw']) ? $_SESSION['form-data']['psw'] : "" ?>>
				</div>

				<div class="mb-3">
					<label class="form-label" for="psw-repeat headTitle"><b>Repeat Password</b></label>
					<input type="password" class="form-control" placeholder="Repeat Password" name="psw-repeat" maxlength="80" required id="psw-repeat" value=<?php echo isset($_SESSION['form-data']['psw-repeat']) ? $_SESSION['form-data']['psw-repeat'] : "" ?>>
				</div>

				<div class="mb-3">
					<label class="form-label" for="p-number"><b>Phone Number <span style="color:grey">(without code)</span></b></label>
					<input type="text" class="form-control" placeholder="Enter Phone Number" name="pnumber" maxlength="10" required id="p-number" value=<?php echo isset($_SESSION['form-data']['pnumber']) ? $_SESSION['form-data']['pnumber'] : "" ?>>
				</div>

				<div class="mb-3">
					<label class="form-label" for="sec-questSelector"><b>Security Question</b></label>
					<select class="form-control" required name="sec-questSelector">
						<option value="Favorite Color">Favorite Color</option>
						<option value="Favorite Car">Favorite Car</option>
						<option value="Favorite Location">Favorite Location</option>
					</select>
				</div>

				<div class="mb-3">
					<input type="text" style="margin: 0px" class="form-control" required placeholder="Answer" name="secans" id="sec-ans" value=<?php echo isset($_SESSION['form-data']['secans']) ? $_SESSION['form-data']['secans'] : "" ?>>
				</div>

				<!--
					<input type="text" placeholder="Enter Security Question" name="secquest" id="sec-quest" value=<?php //echo isset($_SESSION['form-data']['secquest']) ? $_SESSION['form-data']['secquest'] : "" ?>>
					<input type="text" style="margin: 0px" placeholder="Answer" name="secans" id="sec-ans" value=<?php //echo isset($_SESSION['form-data']['secans']) ? $_SESSION['form-data']['secans'] : "" ?>>
				-->

				<div class="mb-3">
					<label class="form-label" for="pro-image"><b>Profile Image <span style="color:grey">(Default if empty)</span></b></label>
					<input class="form-control" type="file" placeholder="Select and Upload" name="profileimage" id="pro-image">
				</div>


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

</html>