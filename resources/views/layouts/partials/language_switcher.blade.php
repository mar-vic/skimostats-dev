<div class="mr-2 position-relative dropdown__container no-vue d-inline-flex">
    <button class="badge badge--custom dropdown__toggle badge-active">
        {{ $current_locale }}
        <i class="fas fa-caret-down"></i>
    </button>

    <div class="dropdown__menu dropdown__menu--right text-nowrap text-left">
        @foreach($available_locales as $locale_name => $available_locale)
        <a href="language/{{ $available_locale }}" class="">{{ $locale_name }}</a>
        @endforeach
    </div>
</div>
