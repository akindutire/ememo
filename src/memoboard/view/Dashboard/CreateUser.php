@extend('AuthorizedUser')

@build('title')
Create User
@endbuild

@build('extra_function_invokation')
    models.userPass = 'memo_user'
@endbuild

@build('dynamic_content')

<section class="" style="padding: 70px 0px 10px 0px;">
    <div class="container">

        <h3>Create User</h3>
    </div>
</section>

<section class="row" style="padding: 5px 0px 20px 0px;">
    <div class="container">

        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">

                <form name="CreateUserFrm" method="post">

                    <div class="row" style="margin: 16px 16px !important;">

                        <div class="col-sm-12 form-group">
                            <label for="fullname">Department</label>
                            <input type="text" name="fullname" ng-model="models.userFullName" ng-required="true" class="form-control input-lg" placeholder="Department_Name">

                        </div>

                        <div class="col-sm-12 form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" ng-model="models.userName" ng-required="true" class="form-control input-lg" placeholder="Username">

                        </div>

                        <div class="col-sm-12 form-group">
                            <label for="password">Default Password</label>
                            <input type="text" name="password" readonly value="memo_user" ng-model="models.userPass" ng-required="true" class="form-control input-lg" placeholder="Password">
                        </div>

                    </div>


                </form>


            </div>




        </div>
        <div class="row">
            <input type="button" ng-click=createUser($event) data-url="{! route('api/create-new-user') !}" ng-disabled="!CreateUserFrm.$valid" class="btn brows-btn" value="Create" />
        </div>
    </div>
</section>

@endbuild