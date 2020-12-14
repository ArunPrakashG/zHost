<?php
require_once '../Core/Config.php';
require_once '../Core/UserModel.php';
require_once '../Common/Functions.php';
require_once '../Core/SessionCheck.php';

if (Config::DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (!isset($_SESSION)) {
    session_start();
}

$_SESSION['PageTitle'] = $_SESSION['selectedMail']['Subject'];

// check for user logged in because
// if user tried to directly access this page via url, he should be redirected back to login and terminate this view
$User;

if (!IsUserLoggedIn()) {
    Functions::Alert("Session expired!\nYou will be required to login again.");
    Functions::Redirect("../Views/LoginView.php");
    exit();
}

$User = unserialize($_SESSION["userDetails"]);
$time = strtotime($User->DateCreated);
$AccountCreationDate = date("d/m/y g:i A", $time);
$User->UserName = $User->FirstName . " " . $User->LastName;

if (!isset($_SESSION['selectedMail']) || empty($_SESSION['selectedMail'])) {
    echo '<script>onNoSelectedMail();</script>';
}

// use post method, post the selected row email data to this script, display
?>

<html>

<head>
    <?php require_once '../Common/Header.php'; ?>
    <link rel="stylesheet" href="../includes/css/EmailView.css" />
    <link rel="stylesheet" href="../includes/css/bootstrap.min.css" />
    <script src="../includes/js/bootstrap.bundle.min.js"></script>
    <script src="../includes/js/EmailViewScript.js"></script>
    <script type="text/javascript">
        <?php echo "var msg =" . json_encode($_SESSION['selectedMail']) . ";" ?>
    </script>
</head>

<body>
    <div class="email-head-sender d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center">
            <div class="avatar">
                <img src="<?php echo $User->AvatarPath ?>" style="width: 50px; height: 50px" alt="Avatar" class="rounded-circle user-avatar-md">
            </div>
            <div class="sender d-flex align-items-center">
                <?php 
                    if($_GET['refer'] == "draft" || $_GET['refer'] == "send"){
                        echo '<a href="#">To ' . $_SESSION['selectedMail']['To'] . '</a> <span>by</span><a href="#">me</a>';
                    }
                    
                    if($_GET['refer'] == "inbox"){
                        echo '<a href="#">From ' . $_SESSION['selectedMail']['From'] . '</a> <span>to</span><a href="#">me</a>';
                    }
                ?>               
            </div>
        </div>
        <div class="date">
            <p><?php echo $_SESSION['selectedMail']['At']; ?></p>
            <p></p>
        </div>
    </div>
    </div>
    <div class="email-body">
        <p><b><?php echo $_SESSION['selectedMail']['Subject']; ?></b></p>
        <p>
            <?php
            echo $_SESSION['selectedMail']['Body'];
            ?>
        </p>
    </div>
    <div class="email-attachments">
        <div class="title">Attachments <span>(<?php echo !empty($_SESSION['selectedMail']['AttachmentPath']) ? "1 File(s)," : "No Attachments," ?> <?php echo Functions::size_as_kb(filesize($_SESSION['selectedMail']['AttachmentPath'])); ?>)</span></div>
        <ul>
            <?php
            if (!empty($_SESSION['selectedMail']['AttachmentPath'])) {
                echo '<li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file">';
                echo '<path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>';
                echo '<polyline points="13 2 13 9 20 9"></polyline>';
                echo "</svg><a href='" . $_SESSION["selectedMail"]["AttachmentPath"] . "' download>" . basename($_SESSION["selectedMail"]["AttachmentPath"]) . "</a><span class='text-muted tx-11'>(" . Functions::size_as_kb(filesize($_SESSION['selectedMail']['AttachmentPath'])) . ")</span></a></li>";

                echo '<div class="card" style="width: 18rem;">';
                echo "<img src='" . $_SESSION["selectedMail"]["AttachmentPath"] . "' class='card-img-top'>";
                //echo '<div class="card-body">';
                //echo '<p class="card-text">Some quick example text to build on the card title and make up the bulk of the cards content.</p>';
                //echo '</div>';
                echo '</div>';
            }
            ?>
        </ul>
        <button style="margin-top: 30px;" class="btn btn-primary btn-lg" onclick="window.history.back();" type="button">Go Back</button>
        <?php
        if ($_GET['refer'] == "inbox" || $_GET['refer'] == "send") {
            echo '<button style="margin-top: 30px;" class="btn btn-primary btn-lg" onclick="quickEdit(msg);" type="button">Edit</button>';
            echo '<button style="margin-top: 30px;" class="btn btn-primary btn-lg" onclick="quickReply(msg.From);" type="button">Quick Reply</button>';
            echo '<button style="margin-top: 30px;" class="btn btn-warning btn-lg" onclick="trashMail(msg.MailID);" type="button">Trash Mail</button>';
        }

        if ($_GET['refer'] == "draft") {
            echo '<button style="margin-top: 30px;" class="btn btn-primary btn-lg" onclick="quickEdit(msg);" type="button">Edit</button>';
        }

        if($_GET['refer'] == "trash"){
            echo '<button style="margin-top: 30px;" class="btn btn-primary btn-lg" onclick="quickReply(msg.From);" type="button">Quick Reply</button>';
        }
        ?>
    </div>
    </div>
    </div>
</body>

</html>