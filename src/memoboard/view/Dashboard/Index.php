@extend('AuthorizedUser')

@build('title')
    Memo Board
@endbuild

@build('dynamic_content')

    {!! $UsrDetails = (new \src\memoboard\service\UserInfoScrapper())->getDetails()  !!}

    {!! $MemoCount = data('MemoInfo') !!}
    {!! $Memos = data('MemoAssociatedToMe') !!}

    <section class="counter" style="padding: 16px 0px;">
    <div class="container">
        <div class="col-md-4 col-sm-4">
            <div class="counter-text">

                <h5><span aria-hidden="true" class="icon-briefcase"></span> {! $MemoCount['all'] !} Memo</h5>

            </div>
        </div>

        <div class="col-md-4 col-sm-4">
            <div class="counter-text">

                <h5><span aria-hidden="true" class="icon-expand"></span> </sp>{! $MemoCount['read'] !} Read</h5>

            </div>
        </div>

        <div class="col-md-4 col-sm-4">
            <div class="counter-text">

                <h5><span aria-hidden="true" class="icon-envelope"></span> {! $MemoCount['unread'] !} Unread</h5>

            </div>
        </div>

    </div>
</section>

    <section class="jobs">
    <div class="container">
        <div class="row heading">
            <h2>Memo box</h2>

        </div>
        <div class="companies">

            @if( sizeof($Memos) > 0 )

                @foreach( $Memos as $Memo )
                    <div class="company-list">
                        <div class="row">
                            <div class="col-md-2 col-sm-2">
                                <div class="company-logo">

                                    @if($Memo->ifrom_ID == $UsrDetails->id)

                                        {!! $label = 'Outgoing' !!}
                                        {!! $lookupId = $Memo->ito_ID !!}

                                        <i class="fa fa-arrow-up" style="font-size: 64px; color: lightgreen;"></i>
                                    @else

                                        {!! $label = 'Incoming' !!}
                                        {!! $lookupId = $Memo->ifrom_ID !!}

                                        <i class="fa fa-arrow-down" style="font-size: 64px; color: lightpink;"></i>
                                    @endif

                                    {!! $SenderOrReceiverDetails = (new \src\memoboard\service\UserInfoScrapper())->getAnyUser($lookupId) !!}
                                </div>
                            </div>
                            <div class="col-md-10 col-sm-10">
                                <div class="company-content">
                                    <h3>
                                        @if($Memo->user_is_read_receipt != true && $label == 'Incoming')
                                            {! '<b>'.$Memo->subject.'</b>' !}
                                        @else
                                            {! $Memo->subject !}
                                        @endif

                                        <span class="full-time"><a href="{! route('read-memo/'.$Memo->id) !}" class="btn" style="color: #FFFFFF;">Open</a></span></h3>
                                    <p>
                                        <span class="company-name">
                                            <i class="fa fa-briefcase"></i>{! $SenderOrReceiverDetails->name !}
                                        </span>
                                        <span class="company-location">
                                            <i class="fa fa-map-marker"></i> {! $Memo->created_at !}
                                        </span>

                                        @if($label == 'Outgoing')
                                            <span class="company-location">

                                                @if( $Memo->user_is_read_receipt == true )
                                                    <i class="fa fa-check" style="color: cornflowerblue;">Delivered</i>
                                                @else
                                                    <i class="fa fa-check" style="color: lightgrey;">Sent</i>
                                                @endif

                                            </span>
                                        @endif

                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else

                <p class="text-center text-danger" style="font-size: large;">No Memo found</p>

            @endif


        </div>
<!--        <div class="row">-->
<!--            <input type="button" class="btn brows-btn" value="Brows All Jobs" />-->
<!--        </div>-->
    </div>
</section>

@endbuild
