@extends('layouts.main', ['title' => 'Contributions'])

@section('content')

<div class="container py-5">
    <h1 class="text-uppercase font-weight-bold text-blue">Contributions</h1>
    <div class="mb-5">
        <p>We want to be as complete and accurate as possible. Our ambition is to have complete profiles for thousands of athletes. It isn’t possible to know about every athlete. We are trying to find your social sites, nationality and other info. We rely on our visitors to help us a little bit.</p>
    </div>

    <div class="mb-5">
        <h2 class="mb-2 font-weight-bold">How to contribute? It is easy!</h2>
        <div class="mb-2 font-weight-bold">For an athlete profile:</div>
        <ol>
            <li>Send us an e-mail to <a href="mailto:info@skimostats.com">info@skimostats.com</a> with Subject - Profile update<br>
                Send us the info you have<br>
                Birthplace: <br>
                Birthdate: <br>
                Height: <br>
                Weight: <br>
                And if you have a profile photo, attach it! But it must be profile photo as you can see below. Our goal is to have every year a new photo for each athlete. So we will have also a photo archive. We hope that nobody would send us fake info or photo, but you never know. Please also put a source of your information.<br></li>
            <li>Click “Send it”.</li>
            <li>In a few minutes, you can see the updated profile.</li>
        </ol>
    </div>
    <div class="mb-5">
        <div class="mb-2 font-weight-bold">Races contributions:</div>
        <ol>
            <li>Compose an e-mail to <a href="mailto:info@skimostats.com">info@skimostats.com</a>, Subject: Race to the SkiMo Stats database.</li>
            <li>Send us the name, place, date and type of the race. If it is possible, results from previous years are also important for us, so we can assign the appropriate category to the race for SkiMo Stats Ranking.</li>
            <li>Send us a link where we would find the results.</li>
            <li>If you haven’t link, send us the results ASAP after the end of the race so we can publish them on our site.</li>
        </ol>
        <p>
            Most often you'll find your contributions are processed within a few hours. Our current average processing time is 33 minutes.
        </p>
    </div>
</div>

@endsection
