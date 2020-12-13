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

$_SESSION['PageTitle'] = "Mail";


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

// use post method, post the selected row email data to this script, display
?>

<html>

<head>
    <?php require_once '../Common/Header.php'; ?>
    <link rel="stylesheet" href="../includes/css/EmailView.css" />
    <link rel="stylesheet" href="../includes/css/bootstrap.min.css" />
</head>

<body>
    <div class="email-head-sender d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center">
            <div class="avatar">
                <img src="<?php echo $User->AvatarPath ?>" style="width: 50px; height: 50px" alt="Avatar" class="rounded-circle user-avatar-md">
            </div>
            <div class="sender d-flex align-items-center">
                <a href="#"><?php echo "Sender" ?></a> <span>to</span><a href="#">me</a>
            </div>
        </div>
        <div class="date">Nov 20, 11:20</div>
    </div>
    </div>
    <div class="email-body">
        <p>Hello,</p>
        <br>
        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>
        <br>
        <p>Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna.</p>
        <br>
        <p><strong>Regards</strong>,<br> John Doe</p>
    </div>
    <div class="email-attachments">
        <div class="title">Attachments <span>(3 files, 12,44 KB)</span></div>
        <ul>
            <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                        <polyline points="13 2 13 9 20 9"></polyline>
                    </svg> Reference.zip <span class="text-muted tx-11">(5.10 MB)</span></a></li>
            <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                        <polyline points="13 2 13 9 20 9"></polyline>
                    </svg> Instructions.zip <span class="text-muted tx-11">(3.15 MB)</span></a></li>
            <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                        <polyline points="13 2 13 9 20 9"></polyline>
                    </svg> Team-list.pdf <span class="text-muted tx-11">(4.5 MB)</span></a></li>
        </ul>
    </div>
    </div>
    </div>
</body>

</html>