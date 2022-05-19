@if(!Cookie::has('cookie_consent'))
<div class="homepage__cookies-bar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-center justify-content-md-between flex-wrap flex-md-nowrap text-center text-md-left">
            <div class="flex-grow-1 mb-4 mb-md-0">
                This website is using cookies. We use them to give you the best experience. By continuing to browse the site, you are agreeing to our use of cookies.
            </div>
            <div class="flex-shrink-0 ml-0 ml-md-3">
                <button class="btn btn-sm btn-warning accept-cookies">Continue</button>
                <a href="{{ route('cookies') }}" class="btn btn-primary btn-sm">Learn more</a>
            </div>
        </div>
    </div>
</div>
@endif
