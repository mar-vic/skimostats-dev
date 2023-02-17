$(function(){
    const $topbar = $('.topbar')
    $('.topbar__hamburger').click(function(e) {
        e.preventDefault()
        $topbar.toggleClass('opened')
    })

    $('.table__unshrink').click(function(e){
        e.preventDefault()
        $(this).parent().removeClass('table--shrinked')
        $(this).hide()
    })

    $('.dropdown__container.no-vue .dropdown__toggle').each(function(e) {
        $(this).click(function(e) {
            e.preventDefault()
            $(this).next().toggleClass('opened')
        })
    })

    $(document).click(function(e) {
        $container = $(e.target).closest('.dropdown__container')
        if ($container.length) {
            e.stopPropagation()

            $('.dropdown__container.no-vue').each(function(){
                if ($container[0] != $(this)[0]) {
                    $(this).find('.dropdown__menu.opened').removeClass('opened')
                }
            })
        } else {
            $('.dropdown__container.no-vue .dropdown__menu.opened').removeClass('opened')
        }
    })

    $('.accept-cookies').click(function(e) {
        e.preventDefault()
        setCookie('cookie_consent', 'accepted', 30)
        $('.homepage__cookies-bar').hide()
    })
})

function setCookie(c_name, value, exdays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = c_name + "=" + c_value;
}

window.getInstaFeed = function({
    url,
    target,
}) {
    const $t = $(target)
    $.get('/get-instafeed/?url='+url, (feed) => {
        for(const media of feed) {
            $t.append(`<a title="${media.caption.text}" href="${media.link}" target="_blank" style="position:relative;display:inline-block;background:url(${media.images.standard_resolution.url}) no-repeat center center / cover;width:200px;height:200px;" class="flex-shrink-0"></a>`)
        }
    })
}

// const _ = import('lodash');
// window.trans = (string) => _.get(window.i18n, string);
