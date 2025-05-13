<?php
session_start();
include('connect.php');
include("config.php");
$setup_otp = mt_rand(11111, 99999);

if (isset($_POST['loginbtn'])) {

    $user_phone = $_POST['uname'];
    $user_phone = stripslashes($user_phone);
    $user_phone = mysqli_real_escape_string($conn, $user_phone);
    $sql = "SELECT * FROM dli_a_bio WHERE 	phone_number='$user_phone' ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $count = mysqli_num_rows($result);


    if ($count == 1) {
        while ($info = mysqli_fetch_assoc($result)) {
            $userstatus = $info['status'];
            $usermemid = $info['member_id'];
        }
        //aes encrypt for bdcpassx
        $ApiCrypter = new ApiCrypter();
        $encryptfid = $ApiCrypter->encrypt($usermemid);
        //aes encrypt for bdcpassx
        header("location: test.php?i=" . $encryptfid);
    } else {
        $alertmsg = '<div class="alert custom-alert-1 shadow-sm alert-danger alert-dismissible fade show" role="alert"><svg width="20" height="20" viewBox="0 0 16 16" class="bi bi-x-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg> Sorry this registration does not exist.  <button class="btn btn-close position-relative p-1 ms-auto" type="button" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOMINION LEADERSHIP INSTITUTE A COURSE (DLIa)</title>

    <!-- google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">

    <!-- animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">

    <!-- magnific popup -->
    <link rel="stylesheet" href="assets/css/magnific-popup.css">

    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"
        integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- slik carousel -->
    <link rel="stylesheet" href="assets/css/slick-theme.css">
    <link rel="stylesheet" href="assets/css/slick.css">

    <!-- bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- css -->
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Color switch Alternatives -->
    <link rel="stylesheet" type="text/css" href="color-switch/css/switch.css">
    <link href="color-switch/css/color-2.css" rel="alternate stylesheet" type="text/css" title="color-2">
    <link href="color-switch/css/color-3.css" rel="alternate stylesheet" type="text/css" title="color-3">
    <link href="color-switch/css/color-4.css" rel="alternate stylesheet" type="text/css" title="color-4">
    <link href="color-switch/css/color-5.css" rel="alternate stylesheet" type="text/css" title="color-5">
    <link href="color-switch/css/color-6.css" rel="alternate stylesheet" type="text/css" title="color-6">
    <link href="color-switch/css/color-7.css" rel="alternate stylesheet" type="text/css" title="color-7">
    <link href="color-switch/css/color-8.css" rel="alternate stylesheet" type="text/css" title="color-8">

</head>

<body>
    <!-- This code is use for color chooser, you can delete -->
    <div id="switch-color" class="color-switcher">
        <div class="open"><i class="fas fa-cog"></i></div>
        <h4>COLOR OPTION</h4>
        <ul>
            <li><a class="color-1" onclick="setActiveStyleSheet('main'); return false;" href="#"><i class="fas fa-cog"></i></a> </li>
            <li><a class="color-2" onclick="setActiveStyleSheet('color-2'); return false;" href="#"><i class="fas fa-cog"></i></a> </li>
            <li><a class="color-3" onclick="setActiveStyleSheet('color-3'); return false;" href="#"><i class="fas fa-cog"></i></a> </li>
            <li><a class="color-4" onclick="setActiveStyleSheet('color-4'); return false;" href="#"><i class="fas fa-cog"></i></a> </li>
            <li><a class="color-5" onclick="setActiveStyleSheet('color-5'); return false;" href="#"><i class="fas fa-cog"></i></a> </li>
            <li><a class="color-6" onclick="setActiveStyleSheet('color-6'); return false;" href="#"><i class="fas fa-cog"></i></a> </li>
            <li><a class="color-7" onclick="setActiveStyleSheet('color-7'); return false;" href="#"><i class="fas fa-cog"></i></a> </li>
            <li><a class="color-8" onclick="setActiveStyleSheet('color-8'); return false;" href="#"><i class="fas fa-cog"></i></a> </li>
        </ul>
    </div>
    <!-- end color switch -->


    <div class="body-wrap">
        <!-- header area start -->
        <header class="header-area">
            <nav class="header-nav navbar fixed-top navbar-expand-lg position-absolute w-100">
                <div class="container header-nav-menu">
                    <a class="navbar-brand" href="index.php">
                        <img src="assets/images/logo/logo.png" alt="Header Logo">
                    </a>

                    <div class="collapse navbar-collapse d-none d-lg-block">
                        <ul class="navbar-nav m-auto">
                            <li class="nav-item">
                                <a href="index.php" class="nav-link py-3">Home</a>
                            </li>
                            <li class="nav-item">
                                <a href="#schedule" class="nav-link py-3">Schedule</a>
                            </li>
                        </ul>
                        <div class="mode-and-button d-flex align-items-center">
                            <div class="mode me-md-3">
                                <img src="assets/images/icon/sun.svg" alt="Sun" class="fa-sun" style="display: none;">
                                <img src="assets/images/icon/moon.svg" alt="Moon" class="fa-moon">
                            </div>
                            <button class="header-btn custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Registration</button>
                        </div>
                    </div>

                    <!-- mobile menu -->
                    <div class="mobile-view-header d-block d-lg-none d-flex gap-3 align-items-center">
                        <div class="mode me-md-3">
                            <img src="assets/images/icon/sun.svg" alt="Sun" class="fa-sun" style="display: none;">
                            <img src="assets/images/icon/moon.svg" alt="Moon" class="fa-moon">
                        </div>
                        <button class="header-btn custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Registration</button>

                        <a class="border rounded-1 py-1 px-2 bg-light" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                            <span class="navbar-toggler-icon nav-toggler-icon"></span>
                        </a>

                        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample">
                            <div class="offcanvas-header">
                                <a class="navbar-brand" href="index-2.html">
                                    <img src="assets/images/logo/logo.png" alt="Header Logo">
                                </a>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <div class="dropdown mt-3">
                                    <ul class="navbar-nav m-auto">
                                        <li class="nav-item">
                                            <a href="index.php" class="nav-link py-3">Home</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#schedule" class="nav-link py-3">Schedule</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end mobile menu -->

                </div>
            </nav>

            <?php if (isset($_SESSION['error'])): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: '<?php echo addslashes($_SESSION['error']); ?>',
                            confirmButtonColor: '#d33'
                        });
                    });
                </script>
            <?php unset($_SESSION['error']);
            endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: '<?php echo addslashes($_SESSION['success']); ?>',
                            confirmButtonColor: '#3085d6'
                        });
                    });
                </script>
            <?php unset($_SESSION['success']);
            endif; ?>

            <?php if (isset($_SESSION['success'])) {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Registration Successful!',
                            text: 'Please proceed to the Print ID Card section to obtain your ID.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>";
                // Unset all session variables
                session_unset();

                // Destroy the session
                session_destroy();
            } elseif (isset($_SESSION['error'])) {
                echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Registration Failed!',
                    text: 'Something went wrong. Please try again later.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
            }

            // Unset all session variables
            session_unset();

            // Destroy the session
            session_destroy();

            ?>
            <!-- hero sec start -->
            <section class="hero-sec" style="background-image: url(assets/images/banner/group.png);">
                <div class="hero-slider-wrap">

                    <div class="hero-slider-item overflow-hidden">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-lg-8 col-md-6 order-md-1 order-2">
                                    <div class="slider-item-content-wrap">
                                        <div class="item-content">
                                            <h3 class="item-title1">
                                                DOMINION LEADERSHIP INSTITUTE BASIC COURSE (DLI)
                                            </h3>
                                            <p class="item-sub">
                                                Join us for the class if you are qualified
                                            </p>
                                            <div class="button-group">
                                                <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>
                                                <a style="padding-top:9px" href="#printID" class="custom-btn2">Print ID</a>
                                            </div>
                                            <img src="assets/images/dots/dots.png" alt="Shadow Image" class="dots-1 opacity-50 img-moving-anim2">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 order-md-2 order-1">
                                    <div class="item-image">
                                        <div class="img-1 img-moving-anim1">
                                            <img src="background/02.jpeg" alt="Event template">
                                        </div>
                                        <div class="img-2 img-moving-anim2">
                                            <img src="background/01.jpeg" alt="Event template">
                                        </div>
                                        <img src="assets/images/dots/dots2.png" alt="Shadow Image" class="dots-2 img-moving-anim3">
                                    </div>
                                </div>
                            </div>
                            <div class="highlight-text img-moving-anim4">
                                <strong class="big-title"><span class="text"> 2025</span></strong>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
            <!-- hero sec start -->
        </header>
        <!-- header area end -->


        <div class="container mt-5">
            <hr>
        </div>

        <!-- schedule sec start -->
        <section id="schedule" class="schedule-sec">
            <div class="container">
                <div class="section-head text-center col-xl-8 m-auto mb-5">
                    <span class="label mb-4">Our SChool Schedule 2025</span>
                    <h2 class="title">
                        Our SChool Schedule March 2025
                    </h2>
                </div>
                <div class="schedule-content-wrap">
                    <ul class="nav nav-pills schedule-nav-tab mb-5" id="pills-tab" role="tablist">
                        <li class="nav-item schedule-nav-item" role="presentation">
                            <button class="nav-link active" id="pills-day-1-tab" data-bs-toggle="pill" data-bs-target="#pills-day-1" type="button" role="tab" aria-controls="pills-day-1" aria-selected="true">Day 01 & Day 02</button>
                        </li>
                        <li class="nav-item schedule-nav-item" role="presentation">
                            <button class="nav-link" id="pills-day-2-tab" data-bs-toggle="pill" data-bs-target="#pills-day-2" type="button" role="tab" aria-controls="pills-day-2" aria-selected="false">Day 03 & Day 04</button>
                        </li>
                        <li class="nav-item schedule-nav-item" role="presentation">
                            <button class="nav-link" id="pills-day-3-tab" data-bs-toggle="pill" data-bs-target="#pills-day-3" type="button" role="tab" aria-controls="pills-day-3" aria-selected="false">Day 05 & Day 06</button>
                        </li>
                        <li class="nav-item schedule-nav-item" role="presentation">
                            <button class="nav-link" id="pills-day-4-tab" data-bs-toggle="pill" data-bs-target="#pills-day-4" type="button" role="tab" aria-controls="pills-day-4" aria-selected="true">Day 07 & Day 08</button>
                        </li>
                        <li class="nav-item schedule-nav-item" role="presentation">
                            <button class="nav-link" id="pills-day-5-tab" data-bs-toggle="pill" data-bs-target="#pills-day-5" type="button" role="tab" aria-controls="pills-day-5" aria-selected="false">Day 09 & Day 10</button>
                        </li>
                        <li class="nav-item schedule-nav-item" role="presentation">
                            <button class="nav-link" id="pills-day-6-tab" data-bs-toggle="pill" data-bs-target="#pills-day-6" type="button" role="tab" aria-controls="pills-day-6" aria-selected="false">Day 11 & 12</button>
                        </li>
                        <li class="nav-item schedule-nav-item" role="presentation">
                            <button class="nav-link" id="pills-day-7-tab" data-bs-toggle="pill" data-bs-target="#pills-day-7" type="button" role="tab" aria-controls="pills-day-7" aria-selected="false">Day 13 Day 14</button>
                        </li>

                    </ul>
                    <div class="tab-content schedule-tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-day-1" role="tabpanel" aria-labelledby="pills-day-1-tab" tabindex="0">
                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb item-thumb">-->
                                        <!--   <img src="assets/images/profile1.png" alt="Profile 1">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">DIVINE NATURE</span>
                                            <span class="date d-block">March 10,2025</span>
                                            <span class="time d-block">05:30pm - 07:00pm</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Pastor Vincent Okeke</h4>
                                            <p class="title-desc"> Pastor Jude</p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile2.png" alt="Profile 2">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">DIVINE LOVE</span>
                                            <span class="date d-block">March 10,2025</span>
                                            <span class="time d-block">7:00PM-8:30PM</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">PastorTochukwu Nwokediuko</h4>
                                            <p class="title-desc">Pastor Ify</p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile3.png" alt="Profile 3">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">BLOOD COVENANT</span>
                                            <span class="date d-block">March 11,2025</span>
                                            <span class="time d-block">5:30PM-7:00PM</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Pastor Emma Ewa</h4>
                                            <p class="title-desc">Pastor Jude</p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile4.png" alt="Profile 4">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">UNDERSTANDING RIGHTEOUSNESS</span>
                                            <span class="date d-block">March 11,2025</span>
                                            <span class="time d-block">7:00PM-8:30PM</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Pastor Chijioke</h4>
                                            <p class="title-desc">Pastor Chinedu Ohajunwa</p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-day-2" role="tabpanel" aria-labelledby="pills-day-2-tab" tabindex="0">
                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile2.png" alt="Profile 2">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">DYNAMICS OF HOLINESS</span>
                                            <span class="date d-block">March 12,2025</span>
                                            <span class="time d-block">5:30PM-7:00PM</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Pastor Freeman</h4>
                                            <p class="title-desc">Pastor Moses Ugboh</p>
                                        </div>
                                        <!--<div class="card-buttoncol-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>

                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile1.png" alt="Profile 1">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">WORD FOUNDATION</span>
                                            <span class="date d-block">March 12,2025</span>
                                            <span class="time d-block">7:00PM-8:30PM</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Pastor Innocent</h4>
                                            <p class="title-desc">Pastor Chinedu NCF Dominate</p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>

                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile3.png" alt="Profile 3">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Monica Smith</span>
                                            <span class="date d-block">October 2, 2023</span>
                                            <span class="time d-block">10:00 - 10:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Events and Networking</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.</p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile4.png" alt="Profile 4">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Vincent Smith</span>
                                            <span class="date d-block">October2,2023</span>
                                            <span class="time d-block">08:00 - 08:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Luminary Sessions</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.</p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-day-3" role="tabpanel" aria-labelledby="pills-day-3-tab" tabindex="0">
                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile3.png" alt="Profile 3">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Monica Smith</span>
                                            <span class="date d-block">October 2, 2023</span>
                                            <span class="time d-block">10:00 - 10:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Events and Networking</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>

                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile1.png" alt="Profile 1">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Stella Smith</span>
                                            <span class="date d-block">October2,2023</span>
                                            <span class="time d-block">08:00 - 08:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Presentation and Keynotes</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile2.png" alt="Profile 2">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Thomas Smith</span>
                                            <span class="date d-block">October 2, 2023</span>
                                            <span class="time d-block">09:00 - 09:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Sessions and Labs</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>

                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile4.png" alt="Profile 4">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Vincent Smith</span>
                                            <span class="date d-block">October2,2023</span>
                                            <span class="time d-block">08:00 - 08:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Luminary Sessions</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-day-4" role="tabpanel" aria-labelledby="pills-day-4-tab" tabindex="0">
                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile4.png" alt="Profile 4">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Vincent Smith</span>
                                            <span class="date d-block">October2,2023</span>
                                            <span class="time d-block">12:00 - 12:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Luminary Sessions</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>

                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile1.png" alt="Profile 1">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Stella Smith</span>
                                            <span class="date d-block">October2,2023</span>
                                            <span class="time d-block">08:00 - 08:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Presentation and Keynotes</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>

                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile2.png" alt="Profile 2">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Thomas Smith</span>
                                            <span class="date d-block">October 2, 2023</span>
                                            <span class="time d-block">09:00 - 09:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Sessions and Labs</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>

                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile3.png" alt="Profile 3">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Monica Smith</span>
                                            <span class="date d-block">October 2, 2023</span>
                                            <span class="time d-block">10:00 - 10:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Events and Networking</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-day-5" role="tabpanel" aria-labelledby="pills-day-5-tab" tabindex="0">
                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile3.png" alt="Profile 3">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Monica Smith</span>
                                            <span class="date d-block">October 2, 2023</span>
                                            <span class="time d-block">10:00 - 10:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Events and Networking</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>

                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile1.png" alt="Profile 1">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Stella Smith</span>
                                            <span class="date d-block">October2,2023</span>
                                            <span class="time d-block">08:00 - 08:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Presentation and Keynotes</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile2.png" alt="Profile 2">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Thomas Smith</span>
                                            <span class="date d-block">October 2, 2023</span>
                                            <span class="time d-block">09:00 - 09:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Sessions and Labs</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>

                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile4.png" alt="Profile 4">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Vincent Smith</span>
                                            <span class="date d-block">October2,2023</span>
                                            <span class="time d-block">08:00 - 08:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Luminary Sessions</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-day-6" role="tabpanel" aria-labelledby="pills-day-6-tab" tabindex="0">
                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile1.png" alt="Profile 1">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Stella Smith</span>
                                            <span class="date d-block">October2,2023</span>
                                            <span class="time d-block">08:00 - 08:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Presentation and Keynotes</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile2.png" alt="Profile 2">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Thomas Smith</span>
                                            <span class="date d-block">October 2, 2023</span>
                                            <span class="time d-block">09:00 - 09:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Presentation and Keynotes</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile3.png" alt="Profile 3">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Monica Smith</span>
                                            <span class="date d-block">October 2, 2023</span>
                                            <span class="time d-block">10:00 - 10:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Presentation and Keynotes</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile4.png" alt="Profile 4">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Stella Smith</span>
                                            <span class="date d-block">October2,2023</span>
                                            <span class="time d-block">08:00 - 08:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Presentation and Keynotes</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-day-7" role="tabpanel" aria-labelledby="pills-day-7-tab" tabindex="0">
                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile3.png" alt="Profile 3">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Monica Smith</span>
                                            <span class="date d-block">October 2, 2023</span>
                                            <span class="time d-block">10:00 - 10:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Events and Networking</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>

                            <div class="row schedule-item" data-aos="fade-right" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule1.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile1.png" alt="Profile 1">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Stella Smith</span>
                                            <span class="date d-block">October2,2023</span>
                                            <span class="time d-block">08:00 - 08:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Presentation and Keynotes</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile2.png" alt="Profile 2">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Thomas Smith</span>
                                            <span class="date d-block">October 2, 2023</span>
                                            <span class="time d-block">09:00 - 09:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Sessions and Labs</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>

                            <div class="row schedule-item" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1000" style="background-image: url(assets/images/banner/schedule2.png);">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-around">
                                        <!--<div class="card-thumb">-->
                                        <!--   <img src="assets/images/profile4.png" alt="Profile 4">-->
                                        <!--</div>-->
                                        <div class="card-description">
                                            <span class="name d-block">Vincent Smith</span>
                                            <span class="date d-block">October2,2023</span>
                                            <span class="time d-block">08:00 - 08:45</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class=" d-flex align-items-center justify-content-between">
                                        <div class="card-title-area col-7">
                                            <h4 class="title">Luminary Sessions</h4>
                                            <p class="title-desc">Discover the latest trends in creativity and get inspired by creative leaders.<a href="#">Read More</a></p>
                                        </div>
                                        <!--<div class="card-button col-5">-->
                                        <!--   <button class="custom-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=" text-center py-3">
                        <button class="custom-btn schedule-btn">Download</button>
                    </div>
                    <div class="dots img-moving-anim1">
                        <img src="assets/images/dots/dots2.png" alt="Shadow Image">
                    </div>
                </div>
            </div>
            <div class="shape">
                <img src="assets/images/shape/2.svg" alt="Shape">
            </div>
        </section>
        <!-- schedule sec end -->

        <!-- brand sec start -->
        <div id="sponsors" class="brand-sec">
            <div class="container">
                <div class="brand-items-wrap d-md-flex text-center justify-content-around align-items-center" data-aos="fade-up" data-aos-duration="1000">
                    <div class="brand-item mb-3">
                        <div class="icon">
                            <img src="#"> DC
                        </div>
                    </div>
                    <div class="brand-item mb-3">
                        <div class="icon">
                            <img src="#"> DLI
                        </div>
                    </div>
                    <div class="brand-item mb-3">
                        <div class="icon">
                            <img src="#"> Stitchitin
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- brand sec end -->


        <div class="container">
            <hr>
        </div>


        <!-- pricing sec start -->
        <section id="pricing" class="pricing-sec">
            <div class="container">
                <div class="section-head col-xl-8 m-auto text-center px-5">
                    <span class="label">DLI (Basic) price list</span>
                    <h2 class="title mb-4">
                        DOMINION LEADERSHIP INSTITUTE BASIC COURSE (DLI)
                    </h2>
                    <p class="desc mb-5">
                        Please make sure you make payment befor you submit the form
                    </p>
                </div>
                <div class="pricing-cart-wrap">
                    <div class="row row-cols-1 ">
                        <div class="col col-md-12 col-lg-12s">
                            <div class="card  h-100" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="2000">
                                <div class="card-body">
                                    <span class="card-lable"><i class="fa-sharp fa-solid fa-circle"></i>DLI REGISTRATION FEE</span>
                                    <h3 class="price-pacage">N10,000 <span class="regular-price"></span>
                                    </h3>
                                    <ul>
                                        <li><a href="#"><i class="fa-solid fa-check"></i>Account Number -1025536080</a></li>
                                        <li><a href="#"><i class="fa-solid fa-check"></i>Account Name - Dominion City Awka </a></li>
                                        <li><a href="#"><i class="fa-solid fa-check"></i>Bank - UBA </a></li>
                                    </ul>
                                    <div class="card-btn">
                                        <button class="custom-btn custom-btn2 mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</button>
                                        <span class="card-footer-label">Please make sure you make payment befor you submit the form</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="dots img-moving-anim1">
                        <img src="assets/images/dots/dots12.png" alt="Shadow Image">
                    </div>
                </div>
            </div>
            <div class="shape">
                <img src="assets/images/shape/5.svg" alt="Shape">
            </div>
        </section>
        <!-- pricing sec end -->
        <div class="container">
            <hr>
        </div>



        <!-- contact sec start -->
        <section id="printID" class="contact-sec" data-aos="zoom-in" data-aos-duration="1000">
            <div class="container">
                <div class="col-xl-5 section-head text-center m-auto mb-5">
                    <span class="label">Student ID Card</span>
                    <h2 class="title mx-2">
                        Print your DLI I.D Card here
                    </h2>
                </div>
                <div class="contact-wrap bg-none p-0">
                    <div class="dots">
                        <img src="assets/images/dots/dots13.png" alt="Shadow Image" class="contact-dots-1 img-moving-anim2">
                    </div>
                    <div class="contact-wrap row py-4 px-3 contact align-items-center m-0">
                        <div class="col-lg-4">
                            <div class="contact-thumb-wrap" style="background-image: url(assets/images/banner/contact-bg.png);">
                                <div class="contact-content">
                                    <h5 class="title text-white">Important Informattion</h5>
                                    <p class="desc">
                                        Provide your email or phone number in any of these formats.
                                        <br> » 8011111111 or 2348011111111
                                    </p>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 mt-4 mt-lg-0">
                            <form class="contact-form" name="signup_form" id="signup_form" enctype="multipart/form-data" method="post" data-anim-visible='fade'>
                                <div class="row gy-3">
                                    <div class="col-lg-6">
                                        <label for="exampleFormControlInput1" class="form-label">Phone No. / Email</label>
                                        <input type="text" class="form-control" id="exampleFormControlInput1" name="uname" id="uname" placeholder="Phone No. / Email" required>
                                    </div>

                                </div>
                                <div class="mb-3">

                                </div>

                                <button class=" custom-btn2" type="submit" onclick="return formValidation()" name="loginbtn" id="loginbtn">Print I.D Card</button>
                            </form>
                        </div>
                    </div>

                    <div class="dots">
                        <img src="assets/images/dots/dots14.png" alt="Shadow Image" class="contact-dots-2 img-moving-anim3">
                    </div>
                </div>
            </div>
        </section>
        <!-- contact sec end -->
        <div class="container">
            <hr>
        </div>



        <!-- footer area start -->
        <footer class="footer-area" style="background-image: url(assets/images/banner/Footer.png);">
            <div class="container">
                <div class="footer-top">
                    <div class="row">
                        <div class=" col-lg-6">
                            <div class="row">
                                <div class="col-sm-6 col-lg-8 ">
                                    <div class="footer-info">
                                        <!-- <a href="index.php" class="footer-logo">
                                 <img src="assets/images/logo/footerlogo.png" alt="Footer Logo">
                              </a> -->
                                        <p class="footer-desc">
                                            DOMINION LEADERSHIP INSTITUTE BASIC COURSE (DLI)
                                        </p>
                                        <ul class="footer-social social">
                                            <li>
                                                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                                            </li>
                                            <li>
                                                <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                                            </li>
                                            <li>
                                                <a href="#"><i class="fa-brands fa-twitter"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <hr>
                <div class="footer-copyright-area text-center pb-3">
                    <span>© 2025 DLI. All rights reserved.</span>
                </div>
            </div>
        </footer>
        <!-- footer area end -->

        <!-- Modal -->
        <div class="modal fade popup-modal" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg popup-dialogue modal-dialog-centered">
                <div class="modal-content popup-content p-4 bg-white">
                    <button type="button" class="btn btn-secondary  ms-auto" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>

                    <div class="modal-body popup-body">
                        <iframe width="100%" height="400" src="https://www.youtube.com/embed/1dtzSRlfBDk" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>

                </div>
            </div>
        </div>

        <!-- Button trigger modal -->

        <!-- Modal 2 -->
        <div class="modal popup-box fade" id="exampleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog popup-box-dialog modal-dialog-centered rounded">
                <div class="modal-content popup-box-content">
                    <div class="popup-card" style="width:100%; overflow-y:auto;">
                        <button type="button" class="btn popup2-btn ms-auto" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <img src="assets/images/dli.png" class="card-img-top" alt="popup-bg">
                        <div class="card-body popup-card-body">
                            <!-- Optional title area can be added here -->
                        </div>

                        <form action="t
sginup_eng2.php" method="post" class="popup-form">
                            <div class="row gy-3 mb-3">
                                <div class="col-lg-6">
                                    <label for="fullname" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Your Full Name" required>
                                </div>
                                <div class="col-lg-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required>
                                </div>
                                <div class="col-lg-6">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Enter Phone Number" required>
                                </div>
                                <div class="col-lg-6">
                                    <label for="chapter" class="form-label">Chapter</label>
                                    <select class="form-control bg-black-50" id="chapter" name="chapter" required>
                                        <option value="">Select Chapter</option>
                                        <option value="DC HQ">1. DC HQ</option>
                                        <option value="DC Abagana">2. DC Abagana</option>
                                        <option value="DC Agu Awka">3. DC Agu Awka</option>
                                        <option value="DC Agulu">4. DC Agulu</option>
                                        <option value="DC Amansea (Glory Center)">5. DC Amansea (Glory Center)</option>
                                        <option value="DC Amawbia">6. DC Amawbia</option>
                                        <option value="DC Amenyi">7. DC Amenyi</option>
                                        <option value="DC Amikwo">8. DC Amikwo</option>
                                        <option value="DC Enugu Agidi">9. DC Enugu Agidi</option>
                                        <option value="DC Ifite 1 (Miracle Arena)">10. DC Ifite 1 (Miracle Arena)</option>
                                        <option value="DC Ifite 2 (Olive Parish)">11. DC Ifite 2 (Olive Parish)</option>
                                        <option value="DC Ifite 3 (Next Level)">12. DC Ifite 3 (Next Level)</option>
                                        <option value="DC Isuaniocha">13. DC Isuaniocha</option>
                                        <option value="DC Nodu">14. DC Nodu</option>
                                        <option value="DC Okpuno (Cedars Parish)">15. DC Okpuno (Cedars Parish)</option>
                                        <option value="DC Tempsite">16. DC Tempsite</option>
                                        <option value="DC Ukwulu">17. DC Ukwulu</option>
                                        <option value="DC Umuokpu">18. DC Umuokpu</option>
                                        <option value="NCF 1 (Elevate)">19. NCF 1 (Elevate)</option>
                                        <option value="NCF 2 (Dominate)">20. NCF 2 (Dominate)</option>
                                        <option value="Mbaukwu Campus">21. Mbaukwu Campus</option>
                                        <option value="YCC">22. YCC</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-control bg-black-50" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label for="marital_status" class="form-label">Marital Status</label>
                                    <select class="form-control bg-black-50" id="marital_status" name="marital_status" required>
                                        <option value="">Select Marital Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="custom-btn2">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



    </div>
    <!-- jquery -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.countdown.min.js"></script>

    <!-- animation -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    <!-- magnific popup -->
    <script src="assets/js/jquery.magnific-popup.min.js"></script>

    <!-- bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- slick -->
    <script src="assets/js/slick.min.js"></script>

    <!-- parallax js -->
    <script src="assets/js/parallax.min.js"></script>

    <!-- javaScript -->
    <script src="assets/js/main.js"></script>

    <!-- color switch -->
    <script src="color-switch/switch.js"></script>


</body>

</html>