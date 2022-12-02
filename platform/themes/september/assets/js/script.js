(function ($) {
    'use strict';

    function backgroundImage() {
        let background = $('[data-background]');
        background.each(function () {
            if ($(this).attr('data-background')) {
                let image_path = $(this).attr('data-background');
                $(this).css({
                    background: 'url(' + image_path + ')'
                });
            }
        });
    }

    function siteToggleAction() {
        let siteOverlay = $('.site-mask');

        $('body').on('click', function (e) {
            if ($(e.target).siblings('.panel--sidebar').hasClass('active')) {
                $('.panel--sidebar').removeClass('active');
                siteOverlay.removeClass('active');
                $('body').css('overflow', 'auto');
            }
        });
    }

    function subMenuToggle() {
        $('.menu--mobile .menu-item-has-children > .sub-toggle').on(
            'click',
            function (e) {
                e.preventDefault();
                let current = $(this).parent('.menu-item-has-children');
                $(this).toggleClass('active');
                current
                    .siblings()
                    .find('.sub-toggle')
                    .removeClass('active');
                current.children('.sub-menu').slideToggle(350);
                current
                    .siblings()
                    .find('.sub-menu')
                    .slideUp(350);
                if (current.hasClass('has-mega-menu')) {
                    current.children('.mega-menu').slideToggle(350);
                    current
                        .siblings('.has-mega-menu')
                        .find('.mega-menu')
                        .slideUp(350);
                }
            }
        );

        $('.menu--mobile .has-mega-menu .mega-menu__column .sub-toggle').on(
            'click',
            function (e) {
                e.preventDefault();
                let current = $(this).closest('.mega-menu__column');
                $(this).toggleClass('active');
                current
                    .siblings()
                    .find('.sub-toggle')
                    .removeClass('active');
                current.children('.sub-menu--mega').slideToggle();
                current
                    .siblings()
                    .find('.sub-menu--mega')
                    .slideUp();
            }
        );
    }

    function slickConfig() {
        let product = $('.product--detail');
        if (product.length > 0) {
            let primary = product.find('.product__gallery'),
                second = product.find('.product__thumbs');

            primary.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                asNavFor: '.product__thumbs',
                rtl: $('body').prop('dir') === 'rtl',
                fade: true,
                dots: false,
                infinite: false,
                arrows: primary.data('arrow'),
                prevArrow: '<a href="#"><i class="fa fa-angle-left"></i></a>',
                nextArrow: '<a href="#"><i class="fa fa-angle-right"></i></a>'
            });

            second.slick({
                slidesToShow: second.data('item'),
                slidesToScroll: 1,
                rtl: $('body').prop('dir') === 'rtl',
                infinite: false,
                arrows: second.data('arrow'),
                focusOnSelect: true,
                prevArrow: '<a href="#"><i class="fa fa-angle-up"></i></a>',
                nextArrow: '<a href="#"><i class="fa fa-angle-down"></i></a>',
                asNavFor: '.product__gallery',
                vertical: second.data('vertical'),
                responsive: [
                    {
                        breakpoint: 1200,
                        settings: {
                            arrows: second.data('arrow'),
                            slidesToShow: 4,
                            vertical: false,
                            prevArrow: '<a href="#"><i class="fa fa-angle-left"></i></a>',
                            nextArrow: '<a href="#"><i class="fa fa-angle-right"></i></a>'
                        }
                    },
                    {
                        breakpoint: 992,
                        settings: {
                            arrows: second.data('arrow'),
                            slidesToShow: 4,
                            vertical: false,
                            prevArrow: '<a href="#"><i class="fa fa-angle-left"></i></a>',
                            nextArrow: '<a href="#"><i class="fa fa-angle-right"></i></a>'
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 3,
                            vertical: false,
                            prevArrow: '<a href="#"><i class="fa fa-angle-left"></i></a>',
                            nextArrow: '<a href="#"><i class="fa fa-angle-right"></i></a>'
                        }
                    }
                ]
            });
        }
    }

    function tabs() {
        $('.tab-list  li > a ').on('click', function (e) {
            e.preventDefault();
            let target = $(this).attr('href');
            $(this)
                .closest('li')
                .siblings('li')
                .removeClass('active');
            $(this)
                .closest('li')
                .addClass('active');
            $(target).addClass('active');
            $(target)
                .siblings('.tab')
                .removeClass('active');
        });

        $('.tab-list.owl-slider .owl-item a').on('click', function (e) {
            e.preventDefault();
            let target = $(this).attr('href');
            $(this)
                .closest('.owl-item')
                .siblings('.owl-item')
                .removeClass('active');
            $(this)
                .closest('.owl-item')
                .addClass('active');
            $(target).addClass('active');
            $(target)
                .siblings('.tab')
                .removeClass('active');
        });
    }

    function rating() {
        $(document).find('select.rating').each(function () {
            let readOnly;
            readOnly = $(this).attr('data-read-only') === 'true';
            $(this).barrating({
                theme: 'fontawesome-stars',
                readonly: readOnly,
                emptyValue: '0'
            });
        });
    }

    function handleSearch() {
        let searchBox = $('.panel--search');
        $('.search-btn').on('click', function (e) {
            e.preventDefault();
            searchBox.addClass('active');
            $('.site-mask').addClass('active');
        });
        $('.panel--search .panel__close').on('click', function (e) {
            e.preventDefault();
            searchBox.removeClass('active');
            $('.site-mask').removeClass('active');
        });
    }

    function togglePanel() {
        $('.panel-trigger').on('click', function (e) {
            e.preventDefault();
            let target = $(this).attr('href');
            $(target).addClass('active');
            $(target)
                .siblings('.panel--sidebar')
                .removeClass('active');
            $('.site-mask').addClass('active');
            $('body').css('overflow', 'hidden');
        });

        $('.panel--sidebar .panel__close').on('click', function (e) {
            e.preventDefault();
            $(this)
                .closest('.panel--sidebar')
                .removeClass('active');
            $('.site-mask').removeClass('active');
            $('body').css('overflow', 'auto');
        });
    }

    function initAnimation(items) {
        items.each(function () {
            let element = $(this);
            let delay = element.attr('data-animation-delay');

            element.css({
                '-webkit-animation-delay': delay,
                '-moz-animation-delay': delay,
                'animation-delay': delay,
                opacity: 0
            });
        });
    }

    function stickyHeader() {
        var header = $('.header'),
            checkpoint = 50;
        header.each(function() {
            if ($(this).data('sticky') === true) {
                var el = $(this);
                $(window).scroll(function() {
                    var currentPosition = $(this).scrollTop();
                    if (currentPosition > checkpoint) {
                        el.addClass('header--sticky');
                    } else {
                        el.removeClass('header--sticky');
                    }
                });
            }
        });
    }

    function productLightbox() {
        var product = $('.product--detail');
        if (product.length > 0 && $('.product__gallery').length) {
            $('.product__gallery').lightGallery({
                selector: '.item a',
                thumbnail: true,
                share: false,
                fullScreen: false,
                autoplay: false,
                autoplayControls: false,
                actualSize: false,
            });
        }
    }

    $(function () {
        backgroundImage();
        siteToggleAction();
        subMenuToggle();
        tabs();
        slickConfig();
        rating();
        handleSearch();
        togglePanel();

        initAnimation($('.animation'));
        initAnimation($('.staggered-animation'), $('.staggered-animation-wrap'));

        $(document).on('translated.owl.carousel', '.owl-slider', function () {
            initAnimation($('.animation'));
            initAnimation($('.staggered-animation'), $('.staggered-animation-wrap'));
        });

        stickyHeader();
        productLightbox();
    });

    $(window).on('load', function () {
        $('body').addClass('loaded');
    });

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        var url = new URL(window.siteUrl);
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = 'expires=' + d.toUTCString();
        document.cookie = cname + '=' + cvalue + '; ' + expires + '; path=/' + '; domain=' + url.hostname;
    }

    function getCookie(cname) {
        var name = cname + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return '';
    }

    $(window).on('load', function () {
        let $modal = $('#flash-sale-modal');
        if ($modal.length && !getCookie($modal.data('id'))) {
            setTimeout(function () {
                $modal.modal('show');
                setCookie($modal.data('id'), 1, 1);
            }, 5000);
        }
    });

    let trans = key => {
        window.trans = window.trans || {};

        return window.trans[key] !== 'undefined' && window.trans[key] ? window.trans[key] : key;
    }

    $(document).ready(function () {
        $('[data-countdown]').each(function () {
            let $this = $(this);
            let finalDate = $(this).data('countdown');
            $this.countdown(finalDate, function (tm) {

                $(this).html(
                    tm.strftime(
                        '<span class="countdown-section"><span class="countdown-amount hover-up">%D</span><span class="countdown-period"> ' + trans('days') + ' </span></span>'
                        + '<span class="countdown-section"><span class="countdown-amount hover-up">%H</span><span class="countdown-period"> ' + trans('hours') + ' </span></span>'
                        + '<span class="countdown-section"><span class="countdown-amount hover-up">%M</span><span class="countdown-period"> ' + trans('mins') + ' </span></span>'
                        + '<span class="countdown-section"><span class="countdown-amount hover-up">%S</span><span class="countdown-period"> ' + trans('sec') + ' </span></span>'
                    )
                );
            });
        });

        var listCategories = $('.ps-list--categories');
        if (listCategories.length > 0) {
            $('.ps-list--categories .menu-item-has-children > .sub-toggle').on(
                'click',
                function(e) {
                    e.preventDefault();
                    var current = $(this).parent('.menu-item-has-children');
                    $(this).toggleClass('active');
                    current
                        .siblings()
                        .find('.sub-toggle')
                        .removeClass('active');
                    current.children('.sub-menu').slideToggle(350);
                    current
                        .siblings()
                        .find('.sub-menu')
                        .slideUp(350);
                    if (current.hasClass('has-mega-menu')) {
                        current.children('.mega-menu').slideToggle(350);
                        current
                            .siblings('.has-mega-menu')
                            .find('.mega-menu')
                            .slideUp(350);
                    }
                }
            );
        }
    });
})(jQuery);
