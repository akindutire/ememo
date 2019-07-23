@extend('AuthorizedUser')

@build('title')
Memo Templates
@endbuild

@build('dynamic_content')

{!! $UsrDetails = (new \src\memoboard\service\UserInfoScrapper())->getDetails()  !!}

{!! $Templates = data('Templates') !!}


<section class="jobs">
    <div class="container">
        <div class="row heading">
            <h2>Template Archive</h2>

        </div>
        <div class="companies">

            <div class="company-list">
                <div class="row">
                    <div class="col-md-2 col-sm-2">
                        <div class="company-logo">

                            <i class="fa fa-plus" style="font-size: 64px; color: crimson;"></i>

                        </div>
                    </div>
                    <div class="col-md-10 col-sm-10">
                        <div class="company-content">

                            <a href="{! route('create-template') !}"><h3>Create New Template</h3></a>

                        </div>
                    </div>
                </div>
            </div>

            @if( sizeof($Templates) > 0 )

                @foreach( $Templates as $Template )
                    <div class="company-list">
                    <div class="row">
                        <div class="col-md-2 col-sm-2">
                            <div class="company-logo">

                                <i class="fa fa-arrow-right" style="font-size: 64px; color: lightgreen;"></i>

                            </div>
                        </div>
                        <div class="col-md-10 col-sm-10">
                            <div class="company-content">
                                <h3>
                                    {! $Template->name !}

                                    <span class="full-time"><a href="{! route('manage-template/'.$Template->id) !}" class="btn" style="color: #FFFFFF;">Manage</a></span></h3>
                                <p>

                                    <span class="company-location">
                                                <i class="fa fa-map-marker"></i> {! $Template->created_at !}
                                            </span>



                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else

                <p class="text-center text-danger" style="font-size: large;">No Template found</p>

            @endif


        </div>
        <!--        <div class="row">-->
        <!--            <input type="button" class="btn brows-btn" value="Brows All Jobs" />-->
        <!--        </div>-->
    </div>
</section>

@endbuild