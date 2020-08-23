<?php

if (!isset($_SESSION)) {
    session_start();
}

$_SESSION['PageTitle'] = "Please wait...";

if (!isset($_GET['path']) || !isset($_GET['name']) || !isset($_GET['header'])) {
    throw new Exception();
}

$RedirectPath = $_GET['path'];
$Header = $_GET['header'];
$Name = $_GET['name'];
?>
<!DOCTYPE html>
<html>

<head>
    <?php require_once '../Common/Header.php' ?>
    <link rel="stylesheet" type="text/css" href="../includes/css/redirect-view-style.css" />
    <link rel="stylesheet" type="text/css" href="../includes/css/three-dots.css" />
    <script src="../includes/js/redirect.js"></script>
    <script type="text/javascript">
        setRedirectPath('<?php echo $RedirectPath ?>', '<?php echo $Name ?>');
    </script>
</head>

<body>
    <div class="container">
    <h2>Please wait...</h2>
        <div class="dot-elastic"></div>
        
        <h4 id="countdownElement">Redirecting you to <?php echo $Name ?> in 3 seconds...</h4>
        <br />
        <div>
            <p>If you are not redirected automatically, <a href=<?php echo $RedirectPath ?>>Click here</a>!</p>
        </div>
    </div>
</body>

</html>