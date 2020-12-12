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

$_SESSION['PageTitle'] = "Dashboard";

// these variables are no longer required or checked at
unset($_SESSION["loginErrorMessage"]);
unset($_SESSION["IsError"]);
unset($_SESSION["registerErrorMessage"]);
unset($_SESSION["IsError"]);
unset($_SESSION["RegistrationMessage"]);
unset($_SESSION["rq"]);

// check for user logged in because
// if user tried to directly access this page via url, he should be redirected back to login and terminate this view
$User;

if (!IsUserLoggedIn()) {
  Functions::Alert("Session expired!\nYou will be required to login again.");
  Functions::Redirect("../Views/LoginView.php");
  exit();
}

$User = unserialize($_SESSION["userDetails"]);
?>

<!doctype html>
<html lang="en">

<head>
  <?php require_once '../Common/Header.php' ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="../includes/css/style-starter.css">
  <link rel="stylesheet" href="../includes/css/dashboard_.css">
  <script src="../includes/js/jquery-1.10.2.min.js"></script>
  <script src="../includes/js/jquery-3.5.1.min.js"></script>
  <script src="../includes/js/bootstrap.bundle.min.js"></script>
  <script src="../includes/js/DashboardViewScript.js"></script>
  <script src="../includes/js/moment.min.js"></script>
</head>

<body class="">
  <section>
    <div class="sidebar-menu sticky-sidebar-menu">
      <div class="logo">
        <h1><a href="../index.php">ZHOST</a></h1>
      </div>
      <div class="logo-icon text-center">
        <a href="dashboard.php" title="logo"><img src="assets/images/logo.png" alt="logo-icon"> </a>
      </div>
      <div class="sidebar-menu-inner">
        <ul class="nav nav-pills nav-stacked custom-nav" id="navItems">
          <li class="active"><a href="../Views/InboxView.php"><span> Inbox</span></a></li>
          <li class=""><a href="#" onclick="onComposeButtonClicked();"><span> Compose</span></a></li>
          <li class=""><a href="../Views/DraftView.php"><span> Draft</span></a></li>
          <li class=""><a href="#" onclick="getSendMails();"><span> Send</span></a></li>
          <li class=""><a href="#" onclick="getTrashMails();"><span> Trash</span></a></li>
        </ul>
      </div>
    </div>
    <div class="header sticky-header">
      <div class="menu-right">
        <div class="navbar user-panel-top">
          <div class="search-box">
            <form action="#" method="get">
              <input class="search-input" placeholder="Search Here..." type="search" id="search">
              <button class="search-submit" value=""><span class="fa fa-search"></span></button>
            </form>
          </div>
          <div class="user-dropdown-details d-flex">
            <div class="profile_details">
              <ul>
                <li class="dropdown profile_details_drop">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="dropdownMenu3" aria-haspopup="true" aria-expanded="false">
                    <div class="profile_img">
                      <img src="<?php echo $User->AvatarPath ?>" class="rounded-circle" alt="" />
                      <div class="user-active">
                        <span></span>
                      </div>
                    </div>
                  </a>
                  <ul class="dropdown-menu drp-mnu" aria-labelledby="dropdownMenu3">
                    <li class="user-info">
                      <h5 class="user-name"><?php echo $User->UserName ?></h5>
                      <span class="status ml-2">Available</span>
                    </li>
                    <li> <a href="#"><i class="lnr lnr-user"></i>My Profile</a> </li>
                    <li class="logout"> <a href="javascript:void(0);" onclick="logoutUser();"><i class="fa fa-power-off"></i> Logout</a> </li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="main-content">
      <div class="container-fluid content-top-gap">
        <div class="card card_border py-2 mb-4">
          <div class="card-body">
            <table id="mailTable" class="table table-striped table-bordered table-responsive">
              <thead>
                <tr>
                  <th scope="col">S.No</th>
                  <th scope="col" class="hidden">Uuid</th>
                  <th scope="col">Sender</th>
                  <th scope="col">Subject</th>
                  <th scope="col">Received Time</th>
                  <th scope="col">Option</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th scope="row">1</th>
                  <td class="hidden">Uuid_PH</td>
                  <td>Name_PH</td>
                  <td>Subject_PH</td>
                  <td>Sender_PH</td>
                  <td>Time_PH</td>
                  <td>
                    <button class="deletebttn btn-light">Button_PH</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
  <footer class="dashboard">
    <p>&copy All Rights Reserved</a> z-Host</p>
  </footer>
  <button onclick="topFunction()" id="movetop" class="bg-primary" title="Go to top">
    <span class="fa fa-angle-up"></span>
  </button>
  <script>
    window.onscroll = function() {
      scrollFunction()
    };

    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("movetop").style.display = "block";
      } else {
        document.getElementById("movetop").style.display = "none";
      }
    }

    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
  </script>
</body>

</html>