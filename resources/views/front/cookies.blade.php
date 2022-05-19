@extends('layouts.main', ['title' => 'Cookies'])

@section('content')

<div class="container py-5">
    <h1 class="text-uppercase font-weight-bold text-blue">Cookie policy</h1>
    <div class="mb-5">
        {{-- <div class="mb-2 font-weight-bold">Cookies</div> --}}
        <p>To make SkiMo Stats work properly, we sometimes place small data files called cookies on your device. Most big websites do this too.</p>
    </div>

    <div class="mb-5">
        <div class="mb-2 font-weight-bold">What are cookies?</div>
        <p>In simple terms, cookies are just files that reside on your computer. Cookies are created when you visit a website. They are used to store bits of information about your interactions with the website, which the web server can use later when processing your sessions (such as login, language, font size and other display preferences. So you don‘t have to keep re-entering them whenever you come back to the site or browse from one page to another.</p>
    </div>
    <div class="mb-5">
        <div class="mb-2 font-weight-bold">How do we use cookies?</div>
        <p>On skimostats.com we make use of cookies for the following purposes:</p>
        <p>- Keep track of traffic<br>
            - Online behavioural advertisements</p>
        <p>Enabling these cookies is not strictly necessary for the website to work but it will provide you with a better browsing experience. You can delete or block these cookies, but if you do that some features of this site may not work as intended. The cookie-related information is not used to identify you personally. These cookies are not used for any purpose other than those described here.</p>
    </div>
    <div class="mb-5">
        <div class="mb-2 font-weight-bold">How to control cookies</div>
        <p>You can control and/or delete cookies as you wish, for details, see aboutcookies.org. You can delete all cookies that are already on your computer and you can set most browsers to prevent them from being placed. If you do this, however, you may have to manually adjust some preferences every time you visit a site and some services and functionalities may not work.</p>
    </div>
</div>

@endsection
