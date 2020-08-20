<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <script src="../includes/js/jquery.min.js" charset="utf-8"></script>
    <script src="../includes/js/sweetalert.min.js"></script>
    <title>zHost - <?php echo $_SESSION['PageTitle'] ?></title>
</head>