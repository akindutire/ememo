@extend('UnauthorizedUser')

@build('title')
    Home
@endbuild

@build('dynamic_content')
<section class="main-banner" style="background:#242c36 url({! uresource('img/slider-01.jpg') !}) no-repeat">
    <div class="container">
        <div class="caption">
            <h2 class="mb-4">Send and Receive Memo</h2>
            <p class="w-75 text-center mt-4"><a href="{! route('login') !}" class="btn btn-lg btn-primary">Get Started</a></p>
        </div>
    </div>
</section>
@endbuild