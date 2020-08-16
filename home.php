<?php require_once('config.php') ?>
<?php require_once(ROOT_PATH . '/includes/functions.php') ?>
<?php session_start(); ?>
<?php $_SESSION['PageTitle'] = "Home" ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<link rel="stylesheet" type="text/css" href="includes/css/dashboard-style.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" charset="utf-8"></script>

<header>
    <label for="check">
        <i class="fas fa-bars" id="sidebar_btn"></i>
    </label>
    <div class="left_area">
        <h3>Coding <span>Snow</span></h3>
    </div>
    <div class="right_area">
        <a href="#" class="logout_btn">Logout</a>
    </div>
</header>

<body>

    <!--
    <p align="center">Home page</p>
    <p>
        <?php echo $_SESSION["Username"]; ?>
    </p>
    -->

    <input type="checkbox" id="check">
    <div class="mobile_nav">
        <div class="nav_bar">
            <img src="includes/images/1.png" class="mobile_profile_image" alt="">
            <i class="fa fa-bars nav_btn"></i>
        </div>
        <div class="mobile_nav_items">
            <a href="#"><i class="fas fa-desktop"></i><span>Dashboard</span></a>
            <a href="#"><i class="fas fa-cogs"></i><span>Components</span></a>
            <a href="#"><i class="fas fa-table"></i><span>Tables</span></a>
            <a href="#"><i class="fas fa-th"></i><span>Forms</span></a>
            <a href="#"><i class="fas fa-info-circle"></i><span>About</span></a>
            <a href="#"><i class="fas fa-sliders-h"></i><span>Settings</span></a>
        </div>
    </div>

    <div class="sidebar">
        <div class="profile_info">
            <img src="/includes/images/1.png" class="profile_image" alt="">
            <h4>Jessica</h4>
        </div>
        <a href="#" class="sidebar-item"><i class="fas fa-desktop"></i><span>Dashboard</span></a>
        <a href="#" class="sidebar-item"><i class="fas fa-cogs"></i><span>Components</span></a>
        <a href="#" class="sidebar-item"><i class="fas fa-table"></i><span>Tables</span></a>
        <a href="#" class="sidebar-item"><i class="fas fa-th"></i><span>Forms</span></a>
        <a href="#" class="sidebar-item"><i class="fas fa-info-circle"></i><span>About</span></a>
        <a href="#" class="sidebar-item"><i class="fas fa-sliders-h"></i><span>Settings</span></a>
    </div>
    <!--sidebar end-->


    <script type="text/javascript">
        $(document).ready(function() {
            $('.nav_btn').click(function() {
                $('.mobile_nav_items').toggleClass('active');
            });
        });
    </script>
</body>

<?php include('footer.php'); ?>

</html>