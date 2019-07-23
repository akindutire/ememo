@extend('AuthorizedUser')

@build('title')
Create Password
@endbuild


@build('dynamic_content')

<section class="" style="padding: 70px 0px 10px 0px;">
    <div class="container">

        <h3>Change Password</h3>
    </div>
</section>

<section class="row" style="padding: 5px 0px 20px 0px;">
    <div class="container">

        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">

                @if( !is_null(errors()) )
                    <p class="bg-danger text-center col-sm-10 col-sm-offset-1">

                        @foreach(errors() as $err)
                            {! ucfirst($err) !}
                        @endforeach
                    </p>
                @endif

                <form name="ChangePasswordFrm" action="{! route('change-pass') !}" method="post">
                    {! csrf !}
                    <div class="row" style="margin: 16px 16px !important;">

                        <div class="col-sm-12 form-group">
                            <label for="OldPass">Password</label>
                            <input type="password" name="OldPass" ng-model="models.p" ng-required="true"  class="form-control input-lg" placeholder="">

                        </div>

                        <div class="col-sm-12 form-group">
                            <label for="NewPass">New Password</label>
                            <input type="password" name="NewPass" ng-model="models.np" ng-required="true" class="form-control input-lg" placeholder="">

                        </div>

                        <div class="col-sm-12 form-group">
                            <label for="ConfirmNewPass">Confirm New Password</label>
                            <input type="password" name="ConfirmNewPass" ng-model="models.cp" ng-required="true" class="form-control input-lg" placeholder="">
                        </div>

                        <div class="col-sm-12">
                            <button type="submit" ng-disabled="!(ChangePasswordFrm.$valid) && (models.np != models.cp)" class="btn btn-primary" style="width: 100%;">Change</button>
                        </div>

                    </div>


                </form>


            </div>




        </div>

    </div>
</section>

@endbuild