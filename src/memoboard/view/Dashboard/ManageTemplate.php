@extend('AuthorizedUser')

@build('title')

{!! $Template = data('MemoTemplate') !!}
{! $Template->name !}

@endbuild

@build('extra_function_invokation')
states.remoteReceipientDetailsUrl = '{! route('api/fetchRecipientDetails') !}';
@endbuild

@build('dynamic_content')


<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js"></script>
<script>tinymce.init({selector:'textarea'});</script>

<section class="" style="padding: 70px 0px 10px 0px;">
    <div class="container">

        <h3 class="w-25">{! strtoupper($Template->name) !}</h3>
    </div>
</section>

<section class="row" style="padding: 5px 0px 20px 0px;">
    <div class="container">

        <div class="companies">

            <div class="company-list">
                <!--Preview-->
                <div class="container" style="width: 100%;">

                    <div class="row">
                        <div class="container" style="padding: 10px 100px 10px 100px; color: black;">
                            {! $Template->template !}
                        </div>

                    </div>

                </div>

            </div>



        </div>

        <div class="row">
            <a href="{! route('use-template/'.$Template->id) !}" class="btn brows-btn">Use</a>
        </div>
    </div>
</section>

@endbuild