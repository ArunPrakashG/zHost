<?php
$_SESSION['PageTitle'] = "Please wait..."
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="../includes/css/spinning-loader.css" />
    <script src="../includes/js/countdown.js"></script>
</head>

<body>
    <div class="container">
        <div class="loader"></div>
        <h2>Please wait...</h2>
        <h4 id="countdownElement">Redirecting to login page in 3 seconds</h4>
        <br />
        <div>
            <p>If you are not redirected automatically, <a href="../Views/LoginView.php">Click here</a>!</p>
        </div>
    </div>
</body>

</html>