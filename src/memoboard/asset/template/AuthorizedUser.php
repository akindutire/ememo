<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>E Memo | @section(title) </title>
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
        ng-controller="BackSideCtrl"
        ng-init="
            @section(extra_function_invokation)
        "
>

{!!    $usrInfo = (new \src\memoboard\service\UserInfoScrapper())->scrap();  !!}
{!!    $EnumRoles = (new \src\memoboard\service\UserEnumRoles())->get(); !!}


<!-- Navigation Start  -->
<nav class="navbar navbar-default navbar-sticky bootsnav">

    <div class="container">
        <!-- Start Header Navigation -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-brand" href=" {! route('dashboard') !} "><img src=" {! uresource('img/logo.png') !} " class="logo" alt=""></a>
        </div>
        <!-- End Header Navigation -->

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="nav navbar-nav navbar-right" data-in="fadeInDown" data-out="fadeOutUp">
                <li><a href="{! route('dashboard') !}">Home</a></li>

                <li><a href="{! route('create-memo') !}">Create Memo</a></li>

                @if($usrInfo['Role'] == $EnumRoles['DIRECTOR'])
                    <li><a href="{! route('memo-template') !}">Memo Templates</a></li>
                @endif

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">More</a>
                    <ul class="dropdown-menu animated fadeOutUp" style="display: none; opacity: 1;">

                        @if($usrInfo['Role'] == $EnumRoles['DIRECTOR'])
                            <li class="active"><a href="{! route('create-user') !}">Create User</a></li>

                        @endif

                        <li><a href="{! route('change-pass') !}">Change Password</a></li>

                    </ul>
                </li>

                <li><a href=" {! route('logout') !} ">Logout</a></li>

            </ul>
        </div><!-- /.navbar-collapse -->
    </div>
</nav>



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
    @import url({! shared('node_modules/ng-img-crop-full-extended/compile/minified/ng-img-crop.css')  !});
</style>
<script src="{! shared('node_modules/toastr/build/toastr.min.js') !}"></script>
<script src="{! shared('node_modules/angular-sanitize/angular-sanitize.min.js') !}"> </script>

<script src="{! shared('node_modules/ng-file-upload/dist/ng-file-upload-all.js') !}"></script>
<script src="{! shared('node_modules/ng-img-crop-full-extended/compile/minified/ng-img-crop.js') !}"></script>

<!--Import angular app-->
<script src="{! asset('js-app/backSide.js') !}"> </script>


</body>
</html>

