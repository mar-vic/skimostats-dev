<!doctype html>
<html lang="en">

<head>
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-TVVWKXT');
    </script>
    <!-- End Google Tag Manager -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - SkiMo Stats' : 'SkiMo Stats' }}</title>

    <meta name="description" content="{{ isset($meta_description) ? $meta_description : 'SkiMo results, calendars, statistics and rankings' }}">
    <meta name="keywords" content="{{ isset($meta_keywords) ? $meta_keywords : 'skimo, ski mountaineering, skimo races, skimo events, vertical races, sprint races, skimo athletes, skimo statistics, skimo stats, skimo rank, skimo ranking, skimo rankings' }}">
    <meta property="og:description" content="{{ isset($meta_description) ? $meta_description : 'SkiMo results, calendars, statistics and rankings' }}">
    <meta property="og:image" content="{{ isset($og_image) ? $og_image : asset('images/og-image-2.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ isset($title) ? $title . ' - Skimostats' : 'Skimostats' }}">

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <script src="https://kit.fontawesome.com/77a1b348c1.js" crossorigin="anonymous"></script>

    <!-- BOOTSTRAP STYLES -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>

    <!-- TAILWIND CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- To avoid conflicts with bootstrap -->
    <script>
        tailwind.config = {
            prefix: "tw-"
        }
    </script>

    <!-- faltpickr styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- flatpickr monthselect plugin style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

    <!-- Styles -->
    <link href="{{ mix('css/main.css') }}" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
</head>

<body>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TVVWKXT" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <header class="topbar">

        <!-- LOGO AND SOCIALS -->
        <div class="container">
            <div class="topbar__top">

                <!-- LOGO -->
                <a href="/"><img src="{{ asset('images/logo.png') }}" class="topbar__logo" /></a>

                <!-- SOCIALS AND MAIL -->
                <div class="topbar__social-icons d-none d-md-flex">
                    <a href='https://www.facebook.com/skimostats' target="_blank" class="topbar__social-icon mr-1">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href='https://www.instagram.com/skimostats/' target="_blank" class="topbar__social-icon mr-1">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href='mailto:info@skimostats.com' class="topbar__social-icon">
                        <i class="far fa-envelope"></i>
                    </a>
                </div>

                <a href="#" class="topbar__hamburger d-flex d-lg-none">
                    <span></span>
                    <span></span>
                    <span></span>
                </a>
            </div>
        </div>
        <!-- END LOGO AND SOCIALS BAR -->


        <!-- MAIN NAV -->
        <nav class="topbar__navigation">
            <div class="container">
                <div class="d-block d-md-none topbar__search" style="width: 100%">
                    <form action="{{ route('search') }}" method="GET" style="display:flex;">
                        <input type="text" name="q" value="{{ Request::get('q') ?? '' }}" placeholder="Search" class="topbar__search-input" style="flex-grow:1;" />
                        <button type="submit" class="topbar__search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <ul>
                    <li><a href="{{ route('landing') }}" {!! !isset($selectedPage) ? ' class="active"' : '' !!}>{{__('Home')}}</a></li>
                    <li><a href="{{ route('races') }}" {!! isset($selectedPage) && $selectedPage=='races' ? " class='active'" : '' !!}>{{__('Races')}}</a></li>
                    <li><a href="{{ route('athletes') }}" {!! isset($selectedPage) && $selectedPage=='athletes' ? " class='active'" : '' !!}>{{__('Athletes')}}</a></li>
                    <li><a href="{{ route('rankings') }}" {!! isset($selectedPage) && $selectedPage=='rankings' ? " class='active'" : '' !!}>{{__('Rankings')}}</a></li>
                    <li><a href="{{ route('statistics') }}" {!! isset($selectedPage) && $selectedPage=='statistics' ? " class='active'" : '' !!}>{{__('Statistics')}}</a></li>
                    <li><a href="{{ route('about-us') }}" {!! isset($selectedPage) && $selectedPage=='about-us' ? " class='active'" : '' !!}>{{__('About us')}}</a></li>
                    <li><a href="{{ route('partners') }}" {!! isset($selectedPage) && $selectedPage=='partners' ? " class='active'" : '' !!}>{{__('Partners')}}</a></li>
                </ul>
                <div class="d-none d-md-block topbar__search">
                    <form action="{{ route('search') }}" method="GET">
                        <input type="text" name="q" value="{{ Request::get('q') ?? '' }}" placeholder="{{__('Search')}}" class="topbar__search-input" />
                        <button type="submit" class="topbar__search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
        <!-- END MAIN NAV -->

    </header>
    <!-- END HEADER -->

    {{-- <div class="homepage__header">
    <div class="container">
                   <div class="homepage__header-subtitle text-uppercase">{{__('Highlight of the day')}}</div>
    <div class="homepage__header-title text-uppercase mb-2">Tour of Taihu</div>
    <a href="" class="homepage__header-read-more">Read article</a>
    </div>
    </div> --}}

    @yield('content')

    <footer class="footer">

        <div class="footer__nav d-flex align-items-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-2 mb-4 mb-md-0">
                        <div class="footer__caption">
                            <a href="{{ route('world-cup') }}">{{__('World Cup')}}</a>
                        </div>
                    </div>
                    <div class="col-md-2 mb-4 mb-md-0">
                        <div class="footer__caption mb-2 mb-md-4">
                            {{__('Grande Course')}}
                        </div>
                        @foreach($footerGrandeCourses as $course)
                        @if($course->event)
                        <a href="{{ route('raceEventDetail.slug', [$course->event->slug]) }}" class="footer__link">{{ $course->name }}</a><br>
                        @endif
                        @endforeach
                    </div>
                    <div class="col-md-3 mb-4 mb-md-0">
                        <div class="footer__caption mb-2 mb-md-4">
                            {{__('Championships')}}
                        </div>

                        <a href="{{ route('race-overview', 'ismf-world-championships') }}" class="footer__link">{{__('World Championships')}}</a>
                        <br><a href="{{ route('race-overview', 'ismf-european-championships') }}" class="footer__link">{{__('European Championships')}}</a>
                        <br><a href="/" class="footer__link">{{__('Pan American Championships')}}</a>
                        <br><a href="/" class="footer__link">{{__('Asian Championships')}}</a>
                    </div>
                    <div class="col-md-3 mb-4 mb-md-0">
                        <div class="footer__caption mb-2 mb-md-4">
                            {{__('Statistics & Rankings')}}
                        </div>

                        <a href="/statistics" class="footer__link">{{__('Statistics')}}</a>
                        <br><a href="/rankings/ismf/2020/men" class="footer__link">{{__('ISMF Rankings')}}</a>
                    </div>
                    <div class="col-md-2">
                        <div class="footer__caption mb-2 mb-md-4">
                            {{__('About SkiMoStats')}}
                        </div>

                        <a href="/about-us" class="footer__link">{{__('About us')}}</a>
                        <br><a href="mailto:info@skimostats.com" class="footer__link">{{__('Contact us')}}</a>
                        <br><a href="{{ route('cookies') }}" class="footer__link">{{__('Cookie policy')}}</a>
                        {{-- <br><a href="/" class="footer__link">{{__('Terms of agreement')}}</a> --}}
                        <br><a href="{{ route('sitemap') }}" target="_blank" class="footer__link">{{__('Site map')}}</a>
                        <br><a href="{{ route('contributions') }}" class="footer__link">{{__('Contributions')}}</a>
                    </div>
                </div>

            </div>
        </div>
        <div class="footer__copyright font-family-heading">
            <span>{{__('copyright')}} © {{ date("Y") }} - {{__('all rights reserved')}}</span>
        </div>
    </footer>

    @include('front.partials.cookies-bar')

    <script src="{{ mix('js/main.js') }}"></script>

            {{-- <script src="/js/lang.js"></script> --}}

    @stack('scripts')
</body>

</html>
