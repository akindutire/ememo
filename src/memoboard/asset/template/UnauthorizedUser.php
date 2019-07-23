<?php
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>E memo | @section(title) </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- All Plugin Css -->
    <link rel="stylesheet" href=" {! uresource('css/plugins.css') !} ">

    <!-- Style & Common Css -->
    <link rel="stylesheet" href=" {! uresource('css/common.css') !} ">
    <link rel="stylesheet" href=" {! uresource('css/main.css') !} ">

    <!--    Import Angular Library-->
    <script src="{! shared('node_modules/angular/angular.min.js') !}"> </script>

</head>

<body
        ng-app="app"
        ng-controller="FrontSideCtrl"
        ng-init="
            states.dashboardUri = '{! route('dashboard') !}';
">

<!-- Navigation Start  -->
<nav class="navbar navbar-default navbar-sticky bootsnav">

    <div class="container">
        <!-- Start Header Navigation -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-brand" href=" {! route('') !} "><img src=" {! uresource('img/logo.png') !} " class="logo" alt=""></a>
        </div>
        <!-- End Header Navigation -->

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="nav navbar-nav navbar-right" data-in="fadeInDown" data-out="fadeOutUp">
                <li><a href=" {! route('') !} ">Home</a></li>
                <li><a href=" {! route('login')  !} ">Login</a></li>
<!--                    <li class="dropdown">-->
<!--                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Browse</a>-->
<!--                    <ul class="dropdown-menu animated fadeOutUp" style="display: none; opacity: 1;">-->
<!--                        <li class="active"><a href="browse-job.html">Browse Jobs</a></li>-->
<!--                        <li><a href="company-detail.html">Job Detail</a></li>-->
<!--                        <li><a href="resume.html">Resume Detail</a></li>-->
<!--                    </ul>-->
<!--                </li>-->
            </ul>
        </div><!-- /.navbar-collapse -->
    </div>
</nav>
<!-- Navigation End  -->

<!--Dynamic Content-->
    @section(dynamic_content)
<!-- footer start -->

<footer>

    <div class="copy-right">
        <p>&copy;OAU E-Memo | {! date('Y') !} </p>
    </div>
</footer>

<script type="text/javascript" src=" {! uresource('js/jquery.min.js') !} "></script>
<script src=" {! uresource('js/bootstrap.min.js') !} "></script>
<script type="text/javascript" src=" {! uresource('js/owl.carousel.min.js') !} "></script>
<script src=" {! uresource('js/bootsnav.js') !} "></script>
<script src=" {! uresource('js/main.js') !} "></script>


<!--Libraries-->
<style>
    @import url({! shared('node_modules/toastr/build/toastr.min.css')  !});
</style>
<script src="{! shared('node_modules/toastr/build/toastr.min.js') !}"></script>
<script src="{! shared('node_modules/angular-sanitize/angular-sanitize.min.js') !}"> </script>

<!--Import angular app-->
<script src="{! asset('js-app/frontSide.js') !}"> </script>


</body>
</html>
