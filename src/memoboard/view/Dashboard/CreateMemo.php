@extend('AuthorizedUser')

@build('title')
    Create Memo
@endbuild

@build('extra_function_invokation')
    states.previewMode = false;
    states.remoteReceipientDetailsUrl = '{! route('api/fetchRecipientDetails') !}';
@endbuild

@build('dynamic_content')

    {!! $AllUsers = data('AllUsers') !!}
    {!! $MyDetails = data('MyInfo') !!}



    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js"></script>
    <script>tinymce.init({
            selector:'textarea',
            plugins: "image, hr, fullscreen, preview",


            image_caption: true
        });</script>

    <section class="" style="padding: 70px 0px 10px 0px;">
        <div class="container">

                <h3 class="w-25">Create/Send your memo</h3> <input type="button" ng-click="states.previewMode = !states.previewMode; updateBodyForState();" class="btn btn-info" value="Toggle Preview" />
        </div>
    </section>

    <section class="row" style="padding: 5px 0px 20px 0px;">
    <div class="container">

        <div class="companies">
            <div class="company-list" ng-show="!states.previewMode">

                <form name="CreateMemoFrm" method="post">

                    <div class="row" style="margin: 16px 16px !important;">
                        <div class="col-sm-6 form-group">
                            <label for="To">Recipient:</label>
                            <select type="text" name="To" ng-model="models.memoTo" ng-required="true" class="form-control input-lg" placeholder="To">

                                @foreach($AllUsers as $User)
                                    <option value="{! $User->id !}">{! $User->name !}</option>
                                @endforach
                            </select>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="Ref">Our Ref:</label>
                            <input type="text" name="Ref" ng-model="models.memoFromRef" ng-required="true" class="form-control input-lg" placeholder="Our Ref.">

                        </div>
                    </div>

                    <div class="row" style="margin: 16px 16px !important;">

                        <div class="col-sm-12">
                            <input type="text" name="subject" ng-model="models.memoSubject" ng-required="true" class="form-control input-lg" placeholder="Subject">

                        </div>
                    </div>

                    <div class="row" style="margin: 16px 16px !important;">
                        <textarea name="Body" id="MemoMessage" cols="30" rows="10"></textarea>
                    </div>


                </form>


            </div>

            <div class="company-list" ng-if="states.previewMode">
                <!--Preview-->
                <div class="container" style="width: 100%;">

                    <div class="row">

                        <div class="col-sm-2 col-md-1" style="padding-right: 2.5em !important;">
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
                            <p><b>FROM:</b> {! $MyDetails->name !}, {! $MyDetails->role !}</p>
                            <p style="margin-top: 0px;"><b>REF:</b> {{models.memoFromRef}}</p>
                        </div>

                        <div class="col-sm-6">
                            <p><b>TO:</b> {{states.receipientDetails.name}}, {{states.receipientDetails.role}}</p>

                        </div>

                    </div>

                    <div class="row">
                        <p class="text-center" style="font-size: 24px; padding-top: 16px; padding-bottom: 32px;"><b><u>INTERNAL MEMORANDUM</u></b></p>
                    </div>

                    <div class="row">
                        <p class="text-center"><b>{{models.memoSubject}}</b></p>
                    </div>

                    <div class="row">
                        <div class="container" style="padding: 10px 100px 10px 100px; color: black;" ng-bind-html="states.TmpMsgBody">

                        </div>

                    </div>

                </div>

            </div>




        </div>
        <div class="row">
            <input type="button" ng-click=sendMemo($event) data-url="{! route('api/send-memo') !}" ng-if="!states.previewMode" ng-disabled="!CreateMemoFrm.$valid" class="btn brows-btn" value="Send" />

        </div>
    </div>
</section>

@endbuild