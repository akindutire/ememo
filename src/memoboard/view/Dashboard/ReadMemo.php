@extend('AuthorizedUser')

@build('title')

    {!! $Memo = data('Memo') !!}
    {! $Memo->subject !}

@endbuild

@build('extra_function_invokation')
    states.remoteReceipientDetailsUrl = '{! route('api/fetchRecipientDetails') !}';
@endbuild

@build('dynamic_content')


{!! $Sender = (new \src\memoboard\service\UserInfoScrapper())->getAnyUser($Memo->ifrom_ID) !!}
{!! $Receiver = (new \src\memoboard\service\UserInfoScrapper())->getAnyUser($Memo->ito_ID) !!}



<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js"></script>
<script>tinymce.init({selector:'textarea'});</script>

<section class="" style="padding: 70px 0px 10px 0px;">
    <div class="container">

        <h3 class="w-25">{! ucwords($Memo->subject) !}</h3>
    </div>
</section>

<section class="row" style="padding: 5px 0px 20px 0px;">
    <div class="container">

        <div class="companies">

            <div class="company-list">
                <!--Preview-->
                <div class="container" style="width: 100%;">

                    <div class="row">

                        <div class="col-sm-2 col-md-1" style="padding-left: 2.5em !important;">
                            <img src="{! uresource('img/oaulogo.png') !}" width="100px" height="auto" alt="">
                        </div>
                        <div class="col-sm-8 col-md-9 p-2">
                            <p class="text-center">INFORMATION TECHNOLOGY AND COMMUNICATIONS UNIT</p>
                            <p class="text-center" style="font-size: 24px;">OBAFEMI AWOLOWO UNIVERSITY</p>
                            <p class="text-center">ILE-IFE, NIGERIA</p>
                            <p class="text-center">Vice-Chancellor Office&nbsp;&nbsp;Tel: 036-232550&nbsp;&nbsp;E-mail: intecu@oauife.edu.ng</p>
                        </div>
                        <div class="col-sm-2 col-md-1" style="padding-right: 2.5em !important;">
                            <img src="{! uresource('img/inteculogo.png') !}" width="100px" height="auto" alt="">
                        </div>
                    </div>

                    <div class="row mx-3" style=" border-top: 1px solid #000; border-bottom: 1px solid #000; ">

                        <div class="col-sm-6" style="border-right: 1px solid #000;">
                            <p><b>FROM:</b>&nbsp;{! $Sender->name !}, {! $Sender->role !}</p>
                            <p style="margin-top: 0px;"><b>REF:</b> {! $Memo->ref !}</p>
                        </div>

                        <div class="col-sm-6">
                            <p><b>TO:</b>&nbsp;{! $Receiver->name !}, {! $Receiver->role !}</p>
                            <p><b>DATE:</b> {! $Memo->created_at !}</p>
                        </div>

                    </div>

                    <div class="row">
                        <p class="text-center" style="font-size: 24px; padding-top: 16px; padding-bottom: 32px;"><b><u>INTERNAL MEMORANDUM</u></b></p>
                    </div>

                    <div class="row">
                        <p class="text-center" style="font-size: 16px;"><b>{! ucwords($Memo->subject) !}</b></p>
                    </div>

                    <div class="row">
                        <div class="container" style="padding: 10px 100px 10px 100px; color: black;">
                            {! $Memo->message !}
                        </div>

                    </div>

                </div>

            </div>



        </div>

    </div>
</section>

@endbuild