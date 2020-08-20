<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<?php if ($_SESSION['PageTitle'] == "Index" || $_SESSION['PageTitle'] == "Login") { ?>
    <?php return; ?>
<?php } else { ?>
    <link rel="stylesheet" type="text/css" href="../includes/css/footer.css" />
    <div class="footer">
        <div id="button"></div>
        <div id="container">
            <div id="cont">
                <div class="footer_center">
                    <h3>zHost <?php echo date("Y"); ?></h3>
                </div>
            </div>
        </div>
    </div>
    
<?php } ?>