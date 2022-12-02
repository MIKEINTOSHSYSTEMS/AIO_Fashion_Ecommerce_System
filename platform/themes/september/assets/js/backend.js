(function ($) {
    'use strict';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function number_format(number, decimals, dec_point, thousands_sep) {
        let n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            toFixedFix = function (n, prec) {
                // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                let k = Math.pow(10, prec);
                return Math.round(n * k) / k;
            },
            s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');

        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }

        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    function filterSlider() {
        $('.nonlinear').each(function (index, element) {
            let $element = $(element);
            let min = $element.data('min');
            let max = $element.data('max');
            let $wrapper = $(element).closest('.nonlinear-wrapper');
            noUiSlider.create(element, {
                connect: true,
                behaviour: 'tap',
                start: [$wrapper.find('.product-filter-item-price-0').val(), $wrapper.find('.product-filter-item-price-1').val()],
                range: {
                    min: min,
                    '10%': max * 0.1,
                    '20%': max * 0.2,
                    '30%': max * 0.3,
                    '40%': max * 0.4,
                    '50%': max * 0.5,
                    '60%': max * 0.6,
                    '70%': max * 0.7,
                    '80%': max * 0.8,
                    '90%': max * 0.9,
                    max: max
                },
            });

            let nodes = [
                $('.ps-slider__min'),
                $('.ps-slider__max'),
            ];

            element.noUiSlider.on('update', function (values, handle) {
                nodes[handle].html(number_format(values[handle]));
            });

            element.noUiSlider.on('end', function (values, handle) {
                $wrapper.find('.product-filter-item-price-' + handle).val(values[handle]);
                $wrapper.find('.product-filter-item').closest('form').submit();
            });
        });
    }

    $(document).ready(function () {

        if (jQuery().mCustomScrollbar) {
            $('.ps-custom-scrollbar').mCustomScrollbar({
                theme: 'dark',
                scrollInertia: 0
            });
        }

        $('.block--method input[name=method]').on('change', function () {
            $(this)
                .closest('.block--method')
                .addClass('active');
            $(this)
                .closest('.block--method')
                .find('.block__content')
                .slideDown();
            $(this)
                .closest('.block--method')
                .siblings('.block--method')
                .removeClass('active');
            $(this)
                .closest('.block--method')
                .siblings('.block--method')
                .find('.block__content')
                .slideUp();
        });

        filterSlider();

        let handleError = function (data, form) {
            if (typeof (data.errors) !== 'undefined' && !_.isArray(data.errors)) {
                handleValidationError(data.errors, form);
            } else if (typeof (data.responseJSON) !== 'undefined') {
                if (typeof (data.responseJSON.errors) !== 'undefined' && data.status === 422) {
                    handleValidationError(data.responseJSON.errors, form);
                } else if (typeof (data.responseJSON.message) !== 'undefined') {
                    $(form).find('.error-message').html(data.responseJSON.message).show();
                } else {
                    let message = '';
                    $.each(data.responseJSON, (index, el) => {
                        $.each(el, (key, item) => {
                            message += item + '<br />';
                        });
                    });

                    $(form).find('.error-message').html(message).show();
                }
            } else {
                $(form).find('.error-message').html(data.statusText).show();
            }
        };

        let handleValidationError = function (errors, form) {
            let message = '';
            $.each(errors, (index, item) => {
                message += item + '<br />';
            });

            $(form).find('.success-message').html('').hide();
            $(form).find('.error-message').html('').hide();

            $(form).find('.error-message').html(message).show();
        };

        window.showAlert = (messageType, message) => {
            if (messageType && message !== '') {
                let alertId = Math.floor(Math.random() * 1000);

                let html = `<div class="alert ${messageType} alert-dismissible" id="${alertId}">
                            <span class="close feather icon-x" data-dismiss="alert" aria-label="close"></span>
                            <i class="feather icon-` + (messageType === 'alert-success' ? 'check-circle': 'alert-circle') + ` message-icon"></i>
                            ${message}
                        </div>`;

                $('#alert-container').append(html).ready(() => {
                    window.setTimeout(() => {
                        $(`#alert-container #${alertId}`).remove();
                    }, 6000);
                });
            }
        }

        var showError = message => {
            window.showAlert('alert-danger', message);
        }

        var showSuccess = message => {
            window.showAlert('alert-success', message);
        }

        $(document).on('click', '.generic-form button[type=submit]', function (event) {
            event.preventDefault();
            event.stopPropagation();
            let buttonText = $(this).html();
            $(this).prop('disabled', true).addClass('btn-disabled').html('<i class="fa fa-spin fa-spinner"></i>');

            $.ajax({
                type: 'POST',
                cache: false,
                url: $(this).closest('form').prop('action'),
                data: new FormData($(this).closest('form')[0]),
                contentType: false,
                processData: false,
                success: res => {
                    $(this).closest('form').find('.success-message').html('').hide();
                    $(this).closest('form').find('.error-message').html('').hide();

                    if (!res.error) {
                        $(this).closest('form').find('input[type=email]').val('');
                        $(this).closest('form').find('input[type=text]').val('');

                        $(this).closest('form').find('.success-message').html(res.message).show();

                        setTimeout(function () {
                            $(this).closest('form').find('.success-message').html('').hide();
                        }, 5000);
                    } else {
                        $(this).closest('form').find('.error-message').html(res.message).show();

                        setTimeout(function () {
                            $(this).closest('form').find('.error-message').html('').hide();
                        }, 5000);
                    }

                    $(this).prop('disabled', false).removeClass('btn-disabled').html(buttonText);
                },
                error: res => {
                    $(this).prop('disabled', false).removeClass('btn-disabled').html(buttonText);
                    handleError(res, $(this).closest('form'));
                }
            });
        });

        let isRTL = $('body').prop('dir') === 'rtl';

        $(document).ready(function () {
            window.onBeforeChangeSwatches = function (data) {
                $('.add-to-cart-form .error-message').hide();
                $('.add-to-cart-form .success-message').hide();
                $('.number-items-available').html('').hide();

                if (data && data.attributes) {
                    $('.add-to-cart-form button[type=submit]').prop('disabled', true).addClass('btn-disabled');
                }
            }

            window.onChangeSwatchesSuccess = function (res) {
                $('.add-to-cart-form .error-message').hide();
                $('.add-to-cart-form .success-message').hide();

                if (res) {
                    let buttonSubmit = $('.add-to-cart-form button[type=submit]');
                    if (res.error) {
                        buttonSubmit.prop('disabled', true).addClass('btn-disabled');
                        $('.number-items-available').html('<span class="text-danger">(' + res.message + ')</span>').show();
                        $('.stock-status-label').hide();
                        $('.hidden-product-id').val('');
                    } else {
                        $('.add-to-cart-form').find('.error-message').hide();
                        $('.product__price .product-sale-price-text').text(res.data.display_sale_price);
                        if (res.data.sale_price !== res.data.price) {
                            $('.product__price .product-price-text').text(res.data.display_price).show();
                        } else {
                            $('.product__price .product-price-text').text(res.data.display_price).hide();
                        }

                        $('.product__info #product-sku').text(res.data.sku);

                        $('.hidden-product-id').val(res.data.id);
                        buttonSubmit.prop('disabled', false).removeClass('btn-disabled');

                        $('.stock-status-label').html('(' + res.data.stock_status_html + ')').show();

                        if (res.data.error_message) {
                            buttonSubmit.prop('disabled', true).addClass('btn-disabled');
                            $('.number-items-available').html('<span class="text-danger">(' + res.data.error_message + ')</span>').show();
                        } else if (res.data.success_message) {
                            $('.number-items-available').html('<span class="text-success">(' + res.data.success_message + ')</span>').show();
                        } else {
                            $('.number-items-available').html('').hide();
                        }

                        const unavailableAttributeIds = res.data.unavailable_attribute_ids || [];
                        $('.attribute-swatch-item').removeClass('pe-none');
                        $('.product-filter-item option').prop('disabled', false);
                        if (unavailableAttributeIds && unavailableAttributeIds.length) {
                            unavailableAttributeIds.map(function (id) {
                                let $item = $('.attribute-swatch-item[data-id="' + id + '"]');
                                if ($item.length) {
                                    $item.addClass('pe-none');
                                    $item.find('input').prop('checked', false);
                                } else {
                                    $item = $('.product-filter-item option[data-id="' + id + '"]');
                                    if ($item.length) {
                                        $item.prop('disabled', 'disabled').prop('selected', false);
                                    }
                                }
                            });
                        }

                        let imageHtml = '';
                        res.data.image_with_sizes.origin.forEach(function (item) {
                            imageHtml += '<div class="item"><a href="' + item + '"><img src="' + item + '" alt="' + res.data.name + '"/></a></div>';
                        });

                        let thumbHtml = '';
                        res.data.image_with_sizes.thumb.forEach(function (item) {
                            thumbHtml += '<div class="item"><img src="' + item + '" alt="' + res.data.name + '"/></div>';
                        });

                        let product = $('.product--detail');
                        let primary = product.find('.product__gallery');
                        let second = product.find('.product__thumbs');

                        primary.slick('unslick');
                        second.slick('unslick');

                        primary.html(imageHtml);
                        second.html(thumbHtml);

                        primary.slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            rtl: isRTL,
                            asNavFor: '.product__thumbs',
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
                            rtl: isRTL,
                            infinite: false,
                            arrows: second.data('arrow'),
                            focusOnSelect: true,
                            prevArrow: '<a href="#"><i class="fa fa-angle-up"></i></a>',
                            nextArrow: '<a href="#"><i class="fa fa-angle-down"></i></a>',
                            asNavFor: '.product__gallery',
                            vertical: true,
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

                        $(window).trigger('resize');

                        if (product.length > 0) {
                            let $gallery = product.find('.product__gallery');
                            if ($gallery.data('lightGallery')) {
                                $gallery.data('lightGallery').destroy(true);
                            }

                            $gallery.lightGallery({
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
                }
            };

            $(document).on('click', '.add-to-cart-button', function (event) {
                event.preventDefault();
                let _self = $(this);

                let buttonText = _self.html();
                _self.prop('disabled', true).addClass('btn-disabled').html('<i class="fa fa-spin fa-spinner"></i>');

                $.ajax({
                    url: _self.data('url'),
                    method: 'POST',
                    data: {
                        id: _self.data('id')
                    },
                    dataType: 'json',
                    success: function (res) {
                        _self.prop('disabled', false).removeClass('btn-disabled').html(buttonText);

                        if (res.error) {
                            window.showAlert('alert-danger', res.message);
                            return false;
                        }

                        if (_self.prop('name') === 'checkout' && res.data.next_url !== undefined) {
                            window.location.href = res.data.next_url;
                        } else {
                            $.ajax({
                                url: window.siteUrl + '/ajax/cart',
                                method: 'GET',
                                success: function (response) {
                                    if (!response.error) {
                                        $('#panel-cart .panel__content').html(response.data.html);
                                        $('.btn-shopping-cart.panel-trigger span').text(response.data.count);
                                        $('.btn-shopping-cart.panel-trigger').trigger('click');
                                    }
                                }
                            });
                        }
                    },
                    error: res => {
                        _self.prop('disabled', false).removeClass('btn-disabled').html(buttonText);
                        window.showAlert('alert-danger', res.message);
                    }
                });
            });

            $(document).on('click', '.add-to-cart-form button[type=submit]', function (event) {
                event.preventDefault();
                event.stopPropagation();

                let _self = $(this);

                if (!$('.hidden-product-id').val()) {
                    _self.prop('disabled', true).addClass('btn-disabled');
                    return;
                }

                let buttonText = _self.html();
                _self.prop('disabled', true).addClass('btn-disabled').html('<i class="fa fa-spin fa-spinner"></i>');

                _self.closest('form').find('.error-message').hide();
                _self.closest('form').find('.success-message').hide();

                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: _self.closest('form').prop('action'),
                    data: new FormData(_self.closest('form')[0]),
                    contentType: false,
                    processData: false,
                    success: res => {
                        _self.prop('disabled', false).removeClass('btn-disabled').html(buttonText);

                        if (res.error) {
                            _self.closest('form').find('.error-message').html(res.message).show();
                            return false;
                        }

                        _self.closest('form').find('.success-message').html(res.message).show();

                        if (_self.prop('name') === 'checkout' && res.data.next_url !== undefined) {
                            window.location.href = res.data.next_url;
                        } else {
                            $.ajax({
                                url: window.siteUrl + '/ajax/cart',
                                method: 'GET',
                                success: function (response) {
                                    if (!response.error) {
                                        $('#panel-cart .panel__content').html(response.data.html);
                                        $('.btn-shopping-cart.panel-trigger span').text(response.data.count);
                                        $('.btn-shopping-cart.panel-trigger').trigger('click');
                                    }
                                }
                            });
                        }
                    },
                    error: res => {
                        _self.prop('disabled', false).removeClass('btn-disabled').html(buttonText);
                        handleError(res, _self.closest('form'));
                    }
                });
            });

            $(document).on('click', '.add-to-wishlist-button', function (event) {
                event.preventDefault();
                let _self = $(this);

                let buttonText = $(this).html();
                _self.html('<i class="fa fa-spin fa-spinner"></i>');

                $.ajax({
                    url: _self.data('url'),
                    method: 'POST',
                    success: res => {

                        if (res.error) {
                            _self.html(buttonText);
                            window.showAlert('alert-danger', res.message);
                            return false;
                        }

                        window.showAlert('alert-success', res.message);

                        $('.btn-shopping-cart.btn-wishlist span').text(res.data.count);

                        _self.html('<i class="fa fa-heart"></i><span>' + _self.data('added-text') + '</span>')
                            .addClass('remove-from-wishlist-button')
                            .removeClass('add-to-wishlist-button')
                    },
                    error: res => {
                        _self.html(buttonText);
                        window.showAlert('alert-danger', res.message);
                    }
                });
            });

            $(document).on('click', '.remove-from-wishlist-button', function (event) {
                event.preventDefault();
                let _self = $(this);

                let buttonText = $(this).html();
                _self.html('<i class="fa fa-spin fa-spinner"></i>');

                $.ajax({
                    url: _self.data('url'),
                    method: 'DELETE',
                    success: res => {

                        if (res.error) {
                            _self.html(buttonText);
                            window.showAlert('alert-danger', res.message);
                            return false;
                        }

                        window.showAlert('alert-success', res.message);

                        $('.btn-shopping-cart.btn-wishlist span').text(res.data.count);

                        _self.html('<i class="fa fa-heart-o"></i><span>' + _self.data('add-text') + '</span>')
                            .removeClass('remove-from-wishlist-button')
                            .addClass('add-to-wishlist-button')
                    },
                    error: res => {
                        _self.html(buttonText);
                        window.showAlert('alert-danger', res.message);
                    }
                });
            });

            $(document).on('click', '.js-add-to-compare-button', function (event) {
                event.preventDefault();
                let _self = $(this);

                const $span = _self.find('span');

                let buttonText = $span.text();

                $span.text(buttonText + '...');

                $.ajax({
                    url: _self.data('url'),
                    method: 'POST',
                    success: res => {

                        if (res.error) {
                            $span.text(buttonText);
                            showError(res.message);
                            return false;
                        }

                        showSuccess(res.message);

                        $('.compare-count span').text(res.data.count);

                        $span.text(buttonText);
                    },
                    error: res => {
                        $span.text(buttonText);
                        showError(res.message);
                    }
                });
            });

            $(document).on('click', '.js-remove-from-compare-button', function (event) {
                event.preventDefault();
                let _self = $(this);

                const $span = _self.find('span');

                let buttonText = $span.text();

                $span.text(buttonText + '...');

                $.ajax({
                    url: _self.data('url'),
                    method: 'DELETE',
                    success: res => {

                        if (res.error) {
                            $span.text(buttonText);
                            showError(res.message);
                            return false;
                        }

                        $('.compare-count span').text(res.data.count);

                        $('.table__compare').load(window.location.href + ' .table__compare > *', function () {
                            showSuccess(res.message);

                            $span.text(buttonText);
                        });
                    },
                    error: res => {
                        $span.text(buttonText);
                        showError(res.message);
                    }
                });
            });

            $(document).on('change', '.shop__sort select', function () {
                $(this).closest('form').submit();
            });

            $(document).on('change', '.product-filter-item', function () {
                $(this).closest('form').submit();
            });

            let imagesReviewBuffer = [];
            let setImagesFormReview = function (input) {
                const dT = new ClipboardEvent('').clipboardData || // Firefox < 62 workaround exploiting https://bugzilla.mozilla.org/show_bug.cgi?id=1422655
                    new DataTransfer(); // specs compliant (as of March 2018 only Chrome)
                for (let file of imagesReviewBuffer) {
                    dT.items.add(file);
                }
                input.files = dT.files;
                loadPreviewImage(input);
            }

            let loadPreviewImage = function (input) {
                let $uploadText = $('.image-upload__text');
                const maxFiles = $(input).data('max-files');
                let filesAmount = input.files.length;

                if (maxFiles) {
                    if (filesAmount >= maxFiles) {
                        $uploadText.closest('.image-upload__uploader-container').addClass('d-none');
                    } else {
                        $uploadText.closest('.image-upload__uploader-container').removeClass('d-none');
                    }
                    $uploadText.text(filesAmount + '/' + maxFiles);
                } else {
                    $uploadText.text(filesAmount);
                }
                const viewerList = $('.image-viewer__list');
                const $template = $('#review-image-template').html();

                viewerList.addClass('is-loading');
                viewerList.find('.image-viewer__item').remove();

                if (filesAmount) {
                    for (let i = filesAmount - 1; i >= 0; i--) {
                        viewerList.prepend($template.replace('__id__', i));
                    }
                    for (let j = filesAmount - 1; j >= 0; j--) {
                        let reader = new FileReader();
                        reader.onload = function(event) {
                            viewerList
                                .find('.image-viewer__item[data-id=' + j + ']')
                                .find('img')
                                .attr('src', event.target.result);
                        }
                        reader.readAsDataURL(input.files[j]);
                    }
                }
                viewerList.removeClass('is-loading')
            }

            $(document).on('change', '.form-review-product input[type=file]', function (event) {
                event.preventDefault();
                let input = this;
                let $input = $(input);
                let maxSize = $input.data('max-size');
                Object.keys(input.files).map(function(i) {
                    if (maxSize && (input.files[i].size / 1024) > maxSize) {
                        let message = $input.data('max-size-message')
                            .replace('__attribute__', input.files[i].name)
                            .replace('__max__', maxSize)
                        window.showAlert('alert-danger', message);
                    } else {
                        imagesReviewBuffer.push(input.files[i]);
                    }
                });

                let filesAmount = imagesReviewBuffer.length;
                const maxFiles = $input.data('max-files');
                if (maxFiles && filesAmount > maxFiles) {
                    imagesReviewBuffer.splice(filesAmount - maxFiles - 1, filesAmount - maxFiles);
                }

                setImagesFormReview(input);
            });

            $(document).on('click', '.form-review-product .image-viewer__icon-remove', function (event) {
                event.preventDefault();
                const $this = $(event.currentTarget);
                let id = $this.closest('.image-viewer__item').data('id');
                imagesReviewBuffer.splice(id, 1);

                let input = $('.form-review-product input[type=file]')[0];
                setImagesFormReview(input);
            });

            if (sessionStorage.reloadReviewsTab) {
                $('.tab-list li a[href="#tab-reviews"]').trigger('click');
                sessionStorage.reloadReviewsTab = false;
            }

            $(document).on('click', '.form-review-product button[type=submit]', function (event) {
                event.preventDefault();
                event.stopPropagation();
                $(this).prop('disabled', true).addClass('btn-disabled').addClass('button-loading');

                const $form = $(this).closest('form');
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: $form.prop('action'),
                    data: new FormData($form[0]),
                    contentType: false,
                    processData: false,
                    success: res => {
                        if (!res.error) {
                            $form.find('select').val(0);
                            $form.find('textarea').val('');

                            showSuccess(res.message);

                            setTimeout(function () {
                                sessionStorage.reloadReviewsTab = true;
                                window.location.reload();
                            }, 1500);
                        } else {
                            showError(res.message);
                        }

                        $(this).prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
                    },
                    error: res => {
                        $(this).prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
                        handleError(res, $form);
                    }
                });
            });
        });

        $(document).on('click', '.product__qty .up', function (event) {
            event.preventDefault();
            event.stopPropagation();
            let currentVal = parseInt($(this).next('.qty-input').val(), 10);
            $(this).next('.qty-input').val(currentVal + 1);

            if ($(this).closest('.section--shopping-cart').length) {
                ajaxUpdateCart($(this));
            }
        });

        $(document).on('click', '.product__qty .down', function (event) {
            event.preventDefault();
            event.stopPropagation();
            let currentVal = parseInt($(this).prev('.qty-input').val(), 10);
            if (currentVal > 0) {
                $(this).prev('.qty-input').val(currentVal - 1);
            }

            if (currentVal >= 0) {
                if ($(this).closest('.section--shopping-cart').length) {
                    ajaxUpdateCart($(this));
                }
            }
        });

        $(document).on('change', '.product__qty .qty-input', function (event) {
            event.preventDefault();
            event.stopPropagation();
            let currentVal = parseInt($(this).val(), 10);
            if (currentVal > 0) {
                $(this).val(currentVal);
            }

            if (currentVal >= 0) {
                if ($(this).closest('.section--shopping-cart').length) {
                    ajaxUpdateCart($(this).closest('.product__qty'));
                }
            }
        });

        function ajaxUpdateCart(_self) {
            _self.closest('.table--cart').addClass('content-loading');

            $.ajax({
                type: 'POST',
                cache: false,
                url: _self.closest('form').prop('action'),
                data: new FormData(_self.closest('form')[0]),
                contentType: false,
                processData: false,
                success: res => {
                    if (res.error) {
                        _self.closest('.table--cart').removeClass('content-loading');
                        window.showAlert('alert-danger', res.message);
                        _self.next('.qty-input').val(res.data.count);
                        return false;
                    }

                    $('.section--shopping-cart').load(window.location.href + ' .section--shopping-cart > *', function () {
                        _self.closest('.table--cart').removeClass('content-loading');
                        window.showAlert('alert-success', res.message);
                    });

                    $.ajax({
                        url: window.siteUrl + '/ajax/cart',
                        method: 'GET',
                        success: response => {
                            if (!response.error) {
                                $('#panel-cart .panel__content').html(response.data.html);
                                $('.btn-shopping-cart.panel-trigger span').text(response.data.count);
                            }
                        }
                    });
                },
                error: res => {
                    _self.closest('.table--cart').removeClass('content-loading');
                    window.showAlert('alert-danger', res.message);
                }
            });
        }

        $(document).on('click', '.remove-cart-item', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.closest('.product--on-cart').addClass('content-loading');

            $.ajax({
                url: _self.data('url'),
                method: 'GET',
                success: res => {
                    _self.closest('li').removeClass('content-loading');

                    if (res.error) {
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    $.ajax({
                        url: window.siteUrl + '/ajax/cart',
                        method: 'GET',
                        success: response => {
                            if (!response.error) {
                                $('#panel-cart .panel__content').html(response.data.html);
                                $('.btn-shopping-cart.panel-trigger span').text(response.data.count);
                                window.showAlert('alert-success', res.message);
                            }
                        }
                    });
                },
                error: res => {
                    _self.closest('.product--on-cart').removeClass('content-loading');
                    window.showAlert('alert-danger', res.message);
                }
            });
        });


        $(document).on('click', '.remove-cart-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.closest('.table--cart').addClass('content-loading');

            $.ajax({
                url: _self.data('url'),
                method: 'GET',
                success: function (res) {

                    if (res.error) {
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    $('.section--shopping-cart').load(window.location.href + ' .section--shopping-cart > *', function () {
                        _self.closest('.table--cart').removeClass('content-loading');
                        window.showAlert('alert-success', res.message);
                    });

                    $.ajax({
                        url: window.siteUrl + '/ajax/cart',
                        method: 'GET',
                        success: response => {
                            if (!response.error) {
                                $('#panel-cart .panel__content').html(response.data.html);
                                $('.btn-shopping-cart.panel-trigger span').text(response.data.count);
                            }
                        }
                    });
                },
                error: res => {
                    _self.closest('.table--cart').removeClass('content-loading');
                    window.showAlert('alert-danger', res.message);
                }
            });
        });

        $(document).on('click', '.js-add-to-wishlist-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            let buttonHtml = $(this).html();

            _self.html('<i class="fa fa-spin fa-spinner"></i>');

            $.ajax({
                url: _self.data('url'),
                method: 'POST',
                success: res => {

                    if (res.error) {
                        _self.html(buttonHtml);
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    window.showAlert('alert-success', res.message);

                    $('.btn-shopping-cart.btn-wishlist span').text(res.data.count);

                    _self.html('<i class="fa fa-heart"></i>').removeClass('js-add-to-wishlist-button').addClass('js-remove-from-wishlist-button active');
                },
                error: res => {
                    _self.html(buttonHtml);
                    window.showAlert('alert-danger', res.message);
                }
            });
        });


        $(document).on('click', '.js-remove-from-wishlist-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            let buttonHtml = $(this).html();
            _self.html('<i class="fa fa-spin fa-spinner"></i>');

            $.ajax({
                url: _self.data('url'),
                method: 'DELETE',
                success: res => {

                    if (res.error) {
                        _self.html(buttonHtml);
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    $('.btn-shopping-cart.btn-wishlist span').text(res.data.count);

                    _self.closest('tr').remove();
                    _self.html('<i class="fa fa-heart-o"></i>').removeClass('js-remove-from-wishlist-button active').addClass('js-add-to-wishlist-button');
                },
                error: res => {
                    _self.html(buttonHtml);
                    window.showAlert('alert-danger', res.message);
                }
            });
        });

        require('../../../../../platform/plugins/language/resources/assets/js/language-public');

    });

})(jQuery);
