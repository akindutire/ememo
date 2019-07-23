@extend('AuthorizedUser')

@build('title')
Create Template
@endbuild



@build('dynamic_content')




<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js"></script>
<script>tinymce.init({
        selector:'textarea',
        plugins: "image, hr, fullscreen, preview",


        image_caption: true
    });</script>

<section class="" style="padding: 70px 0px 10px 0px;">
    <div class="container">

        <h3 class="w-25">Create your template</h3>
    </div>
</section>

<section class="row" style="padding: 5px 0px 20px 0px;">
    <div class="container">

        <div class="companies">
            <div class="company-list">

                <form name="CreateMemoTemplateFrm" method="post">


                    <div class="row" style="margin: 16px 16px !important;">

                        <div class="col-sm-12 form-group">
                            <label for="templateName">TemplateName</label>
                            <input type="text" name="templateName" ng-model="models.templateName" ng-required="true" class="form-control input-lg" placeholder="Template Name">

                        </div>
                    </div>

                    <div class="row" style="margin: 16px 16px !important;">
                        <textarea name="Body" id="TemplateString" cols="30" rows="10"></textarea>
                    </div>


                </form>


            </div>


        </div>
        <div class="row">
            <input type="button" ng-click=createMemoTemplate($event) data-url="{! route('api/create-memo-template') !}"  ng-disabled="!CreateMemoTemplateFrm.$valid" class="btn brows-btn" value="Create" />

        </div>
    </div>
</section>

@endbuild