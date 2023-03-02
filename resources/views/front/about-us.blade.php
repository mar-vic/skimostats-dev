@extends('layouts.main', ['selectedPage' => 'about-us', 'title' => 'About Us'])
@section('content')

<div class="container py-5">
                                         <h1 class="mb-3 mb-md-5 text-uppercase text-blue font-weight-bold">{{__('About us')}}</h1>

    <div class="row mb-5">
        <div class="col-md-2 mb-2 mb-md-0">
            <img src="{{ asset('images/roland.jpg') }}" style="max-width:100%;display:block;" alt="Roland hric">
        </div>
        <div class="col-md-6">
            <div class="font-weight-bold mb-3">
                                         Roland Hric (23 {{__('years')}})
            </div>
            Let me introduce myself. I am a former ice-hockey player and canoeist who fell in love with mountain sports a few years ago. I am a sports journalist with huge experience working with people, sportsmen, data and historical data.
            We (with Randy) helped with something similar years back in cycling. I think that SkiMo needs to
            have an independent and modern communication channel which unites the whole sport &quot;under the
            one roof&quot;.
            <br><br>
            I am responsible for everything connected with the SkiMo Stats. And I really like it.
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-2 mb-2 mb-md-0">
            <img src="{{ asset('images/randy.jpg') }}" style="max-width:100%;display:block;" alt="Randy hric">
        </div>
        <div class="col-md-6">
            <div class="font-weight-bold mb-3">Randy Hric (21 years)</div>
            I would describe myself as a young guy who has loved sports from early childhood and everything about it. I am a former ice-hockey player and canoeist. But then I discovered the magic of movement in the mountains. The main goal of SkiMo Stats is to help this sport grow towards people who love sports. The idea was in my head for a long time as I was browsing through results and profiles of other sports. I told the idea of “skimo stats” to my brother and we started working on it right away. Responsible for everything connected with SkiMo Stats.
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-2 mb-2 mb-md-0">
            <img src="{{ asset('images/igor.jpg') }}" style="max-width:100%;display:block;" alt="Igor Žiak">
        </div>
        <div class="col-md-6">
            <div class="font-weight-bold mb-3">
                Igor Žiak (42 years)
            </div>
            Professional mountain rescue and SkiMo enthusiast. Several times participating in Pierra Menta. Since the beginning of the idea,
            I saw SkiMo Stats as another small yet very important step in SkiMo evolution bringing unparalleled service in our community.
            <br><br>
            My main role in the project is to consult and take care of finances :)
        </div>
    </div>

</div>

@endsection
