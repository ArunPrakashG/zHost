<?php 
    if(!isset($_SESSION)){
        session_start();
    }
?>

<?php if ($_SESSION['PageTitle'] == "Index" || $_SESSION['PageTitle'] == "Login") { ?>
    <?php return; ?>
<?php } else { ?>
    <link rel="stylesheet" type="text/css" href="../includes/css/styling.css" />
    <div class="footer">
        <p>zHost &copy; <?php echo date("Y"); ?> </p>
    </div>
<?php } ?>