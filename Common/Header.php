<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <?php
    // index is located at the root of the site
    // we dont need to go directories behind with ..
    if (strcmp($_SESSION['PageTitle'], "Welcome!") == 0) {
        echo '<script src="includes/js/jquery.min.js" charset="utf-8"></script>';
        echo '<script src="includes/js/sweetalert2.min.js"></script>';
    } else {
        echo '<script src="../includes/js/jquery.min.js" charset="utf-8"></script>';
        echo '<script src="../includes/js/sweetalert2.min.js"></script>';
    }
    ?>

    <title>zHost - <?php echo $_SESSION['PageTitle'] ?></title>
</head>