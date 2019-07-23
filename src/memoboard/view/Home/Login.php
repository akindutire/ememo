@extend('UnauthorizedUser')


@build('title')
    Login
@endbuild


@build('dynamic_content')

    <!-- login section start -->
        <section class="login-wrapper">
            <div class="container">
                <div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">


                        @if( !is_null(notifications()) )
                            <p class="bg-warning text-center col-sm-8 col-sm-offset-2">
                                @foreach(notifications() as $note)
                                    {! ucfirst($note) !}
                                @endforeach
                            </p>
                        @endif


                    <form name="UserLoginFrm" method="post">
                        <img class="img-responsive" alt="logo" src=" {! uresource('img/logo.png') !} ">
                        <input type="text" name="username" ng-model="models.loginUsername" ng-required="true" class="form-control input-lg" placeholder="User Name">
                        <input type="password" name="password" ng-model="models.loginPassword" class="form-control input-lg" placeholder="Password">
<!--                        <label><a href="">Forget Password?</a></label>-->
                        <button type="button" ng-click=login($event) data-url="{! route('api/auth') !}" ng-disabled="!UserLoginFrm.$valid" class="btn btn-primary">Login</button>
                        <!--                <p>Have't Any Account <a href="">Create An Account</a></p>-->
                    </form>
                </div>
            </div>
        </section>
    <!-- login section End -->

@endbuild