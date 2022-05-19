export function isMobile() {
    return $(window).width() < 767
}

export function scrollTo({ el, time = 300, offset = 0 }) {
    $('html,body').stop(true, true).animate({
        scrollTop: $(el).offset().top + offset,
    }, time)
}
