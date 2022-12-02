@php
    Theme::layout('full-width');
    Theme::asset()->usePath()->add('lightGallery-css', 'plugins/lightGallery/css/lightgallery.min.css');
    Theme::asset()->container('footer')->usePath()
        ->add('lightGallery-js', 'plugins/lightGallery/js/lightgallery.min.js', ['jquery']);
@endphp

<main class="page--inner page--product--detail">
    <div class="container">
        <article class="product--detail">
            <div class="product__header">
                <div class="product__thumbnail">
                    <figure>
                        <div class="wrapper">
                            <div class="product__gallery" data-arrow="true">
                                @foreach ($productImages as $img)
                                    <div class="item">
                                        <a href="{{ RvMedia::getImageUrl($img) }}">
                                            <img src="{{ RvMedia::getImageUrl($img) }}" alt="{{ $product->name }}" />
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </figure>
                    <div class="product__variants product__thumbs" data-vertical="true" data-item="5" data-md="3" data-sm="3" data-arrow="false">
                        @foreach ($productImages as $thumb)
                            <div class="item">
                                <img src="{{ RvMedia::getImageUrl($thumb, 'thumb') }}" alt="{{ $product->name }}" />
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="product__info">
                    <div class="product__info-header">
                        <h2 class="product__title">{{ $product->name }}</h2> <span class="stock-status-label">({!! $product->stock_status_html !!})</span>
                    </div>
                    <div>
                        <div>
                            @if ($product->sku)
                                <span class="d-inline-block">{{ __('SKU') }}:</span> <span id="product-sku" class="sku d-inline-block" itemprop="sku">{{ $product->sku }}</span>
                            @endif

                            @if (EcommerceHelper::isReviewEnabled())
                                @if ($product->reviews_count > 0)
                                    @if ($product->sku) - @endif
                                    <div class="rating_wrap d-inline-block">
                                        <div class="rating">
                                            <div class="product_rate" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                                        </div>
                                        <span class="rating_num">({{ $product->reviews_count }})</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="product__price @if ($product->front_sale_price !== $product->price) sale @endif">
                        <p>
                            <span class="product-sale-price-text">{{ format_price($product->front_sale_price_with_taxes) }}</span>
                            <small><del class="product-price-text" @if ($product->front_sale_price == $product->price) style="display: none" @endif>{{ format_price($product->price_with_taxes) }}</del></small>
                        </p>
                        <p>
                            @if (EcommerceHelper::isWishlistEnabled())
                                <a class="product__add-wishlist add-to-wishlist-button" href="#" data-url="{{ route('public.wishlist.add', $product->id) }}" data-add-text="{{ __('Add to wishlist') }}" data-added-text="{{ __('Added to wishlist') }}"><i class="fa fa-heart-o"></i>
                                    <span>{{ __('Add to wishlist') }}</span>
                                </a>
                            @endif
                            @if (EcommerceHelper::isCompareEnabled())
                                <a class="product__add-wishlist ml-3 js-add-to-compare-button" data-url="{{ route('public.compare.add', $product->id) }}" href="#"><i class="feather icon icon-plus-circle"></i> <span>{{ __('Compare') }}</span></a>
                            @endif
                        </p>
                    </div>
                    <div class="product__desc">
                        {!! apply_filters('ecommerce_before_product_description', null, $product) !!}
                        <p>{!! clean($product->description) !!}</p>
                        {!! apply_filters('ecommerce_after_product_description', null, $product) !!}
                    </div>
                    <div class="product__shopping">
                        <div class="row">
                            @if ($product->variations()->count() > 0)
                                <div class="col-sm-12 col-12">
                                    {!! render_product_swatches($product, [
                                        'selected' => $selectedAttrs,
                                        'view'     => Theme::getThemeNamespace() . '::views.ecommerce.attributes.swatches-renderer'
                                    ]) !!}
                                </div>
                            @endif
                        </div>
                        @if (EcommerceHelper::isCartEnabled())
                            <form class="single-variation-wrap add-to-cart-form" method="POST" action="{{ route('public.cart.add-to-cart') }}">
                                @csrf
                                {!! apply_filters(ECOMMERCE_PRODUCT_DETAIL_EXTRA_HTML, null) !!}
                                <input type="hidden" name="id" class="hidden-product-id" value="{{ ($product->is_variation || !$product->defaultVariation->product_id) ? $product->id : $product->defaultVariation->product_id }}"/>
                                <div class="form-group product__attribute product__qty">
                                    <label for="qty-input">{{ __('Qty') }}</label>
                                    <div class="form-group__content">
                                        <div class="form-group--number">
                                            <button type="button" class="up"></button>
                                            <input class="form-control qty-input" name="qty" type="number" value="1" id="qty-input">
                                            <button type="button" class="down"></button>
                                        </div>

                                        <div class="float-right number-items-available" style="@if (!$product->isOutOfStock()) display: none; @endif line-height: 45px;">
                                            @if ($product->isOutOfStock())
                                                <span class="text-danger">({{ __('Out of stock') }})</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" @if ($product->isOutOfStock()) disabled @endif class="btn--custom btn--outline btn--rounded btn-add-cart @if (!EcommerceHelper::isQuickBuyButtonEnabled()) btn--fullwidth @endif @if ($product->isOutOfStock()) btn-disabled @endif">
                                    {{ __('Add to cart') }}
                                </button>
                                @if (EcommerceHelper::isQuickBuyButtonEnabled())
                                    &nbsp;
                                    <button type="submit" name="checkout" @if ($product->isOutOfStock()) disabled @endif class="btn--custom btn--rounded btn-add-cart @if ($product->isOutOfStock()) btn-disabled @endif">
                                        {{ __('Quick Buy') }}
                                    </button>
                                @endif
                                <div class="success-message text-success" style="display: none;">
                                    <span></span>
                                </div>
                                <div class="error-message text-danger" style="display: none;">
                                    <span></span>
                                </div>
                            </form>
                        @endif
                    </div>

                    @if (!$product->tags->isEmpty())
                        <figure class="product__tags">
                            <figcaption>{{ __('Tags') }}:</figcaption>
                            @foreach ($product->tags as $tag)
                                <a href="{{ $tag->url }}">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </figure>
                    @endif
                    <figure class="product__sharing">
                        <figcaption>{{ __('Share') }}:</figcaption>
                        <ul class="list--social">
                            <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}&title={{ rawurldecode($product->description) }}" target="_blank" title="{{ __('Share on Facebook') }}"><i class="feather icon icon-facebook"></i></a></li>
                            <li><a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ rawurldecode($product->description) }}" target="_blank" title="{{ __('Share on Twitter') }}"><i class="feather icon icon-twitter"></i></a></li>
                            <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&summary={{ rawurldecode($product->description) }}&source=Linkedin" title="{{ __('Share on Linkedin') }}" target="_blank"><i class="feather icon icon-linkedin"></i></a></li>
                        </ul>
                    </figure>
                </div>
            </div>
            <div class="product__content tab-root">
                <ul class="tab-list">
                    <li class="active"><a href="#tab-description">{{ __('Description') }}</a></li>
                    @if (EcommerceHelper::isReviewEnabled())
                        <li><a href="#tab-reviews">{{ __('Reviews') }}({{ $product->reviews_count }})</a></li>
                    @endif
                    @if (is_plugin_active('faq'))
                        @if (count($product->faq_items) > 0)
                            <li>
                                <a href="#tab-faq">{{ __('Questions and Answers') }}</a>
                            </li>
                        @endif
                    @endif
                </ul>
                <div class="tabs">
                    <div class="tab active" id="tab-description">
                        <div class="document">
                            {!! clean($product->content) !!}
                        </div>
                    </div>
                    @if (is_plugin_active('faq') && count($product->faq_items) > 0)
                        <div class="tab faqs-list" id="tab-faq">
                            <div class="accordion" id="faq-accordion">
                                @foreach($product->faq_items as $faq)
                                    <div class="card">
                                        <div class="card-header" id="heading-faq-{{ $loop->index }}">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left @if (!$loop->first) collapsed @endif" type="button" data-toggle="collapse" data-target="#collapse-faq-{{ $loop->index }}" aria-expanded="true" aria-controls="collapse-faq-{{ $loop->index }}">
                                                    {!! clean($faq[0]['value']) !!}
                                                </button>
                                            </h2>
                                        </div>

                                        <div id="collapse-faq-{{ $loop->index }}" class="collapse @if ($loop->first) show @endif" aria-labelledby="heading-faq-{{ $loop->index }}" data-parent="#faq-accordion">
                                            <div class="card-body">
                                                {!! clean($faq[1]['value']) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (EcommerceHelper::isReviewEnabled())
                        <div class="tab" id="tab-reviews">
                            @if ($product->reviews_count)
                                @if (count($product->review_images))
                                    <div class="my-3">
                                        <h4>{{ __('Images from customer (:count)', ['count' => count($product->review_images)]) }}</h4>
                                        <div class="block--review">
                                            <div class="block__images row m-0 block__images_total">
                                                @foreach ($product->review_images as $img)
                                                    <a href="{{ RvMedia::getImageUrl($img) }}" class="col-lg-1 col-sm-2 col-3 more-review-images @if ($loop->iteration > 12) d-none @endif">
                                                        <div class="border position-relative rounded">
                                                            <img src="{{ RvMedia::getImageUrl($img, 'thumb') }}" alt="{{ $product->name }}" class="img-responsive rounded h-100">
                                                            @if ($loop->iteration == 12 && (count($product->review_images) - $loop->iteration > 0))
                                                                <span>+{{ count($product->review_images) - $loop->iteration }}</span>
                                                            @endif
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="block--product-reviews">
                                    <div class="block__header">
                                        <h4>{{ __('Reviews for ":product"', ['product' => $product->name]) }}</h4>
                                        @if ($product->reviews_count > 0)
                                            <div class="rating_wrap">
                                                <div class="rating">
                                                    <div class="product_rate" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                                                </div>
                                                <span class="rating_num"> {{ number_format($product->reviews_avg) }} ({{ $product->reviews_count }} {{ __('reviews') }})</span>
                                            </div>
                                        @endif
                                    </div>
                                    <product-reviews-component url="{{ route('public.ajax.product-reviews', $product->id) }}"></product-reviews-component>
                                </div>
                            @endif

                        {!! Form::open(['route' => 'public.reviews.create', 'method' => 'post', 'class' => 'form--review-product form-review-product', 'files' => true]) !!}
                            <h3>{{ __('Submit your review') }}</h3>
                            @if (!auth('customer')->check())
                                <p class="text-danger">{{ __('Please') }} <a href="{{ route('customer.login') }}">{{ __('login') }}</a> {{ __('to write review!') }}</p>
                            @endif
                            <div class="form__content">
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="form__rating">
                                    <label for="select-star">{{ __('Add your rate') }}:</label>
                                    <select class="rating" name="star" id="select-star" data-read-only="false" @if (!auth('customer')->check()) disabled @endif>
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="txt-comment">{{ __('Review') }} <sup>*</sup></label>
                                    <textarea class="form-control" name="comment" id="txt-comment" rows="6" placeholder="{{ __('Write your review') }}" @if (!auth('customer')->check()) disabled @endif></textarea>
                                </div>

                                <div class="form-group">
                                    <script type="text/x-custom-template" id="review-image-template">
                                        <span class="image-viewer__item" data-id="__id__">
                                            <img src="{{ RvMedia::getDefaultImage() }}" alt="Preview" class="img-responsive d-block">
                                            <span class="image-viewer__icon-remove">
                                                <i class="feather icon icon-x"></i>
                                            </span>
                                        </span>
                                    </script>
                                    <div class="image-upload__viewer d-flex">
                                        <div class="image-viewer__list position-relative">
                                            <div class="image-upload__uploader-container">
                                                <div class="d-table">
                                                    <div class="image-upload__uploader">
                                                        <i class="fa fa-image image-upload__icon"></i>
                                                        <div class="image-upload__text">{{ __('Upload photos') }}</div>
                                                        <input type="file"
                                                               name="images[]"
                                                               data-max-files="{{ EcommerceHelper::reviewMaxFileNumber() }}"
                                                               class="image-upload__file-input"
                                                               accept="image/png,image/jpeg,image/jpg"
                                                               multiple="multiple"
                                                               data-max-size="{{ EcommerceHelper::reviewMaxFileSize(true) }}"
                                                               data-max-size-message="{{ trans('validation.max.file', ['attribute' => '__attribute__', 'max' => '__max__']) }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="loading">
                                                <div class="half-circle-spinner">
                                                    <div class="circle circle-1"></div>
                                                    <div class="circle circle-2"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="help-block d-inline-block">
                                            {{ __('You can upload up to :total photos, each photo maximum size is :max kilobytes', [
                                                'total' => EcommerceHelper::reviewMaxFileNumber(),
                                                'max'   => EcommerceHelper::reviewMaxFileSize(true),
                                            ]) }}
                                        </span>
                                    </div>

                                </div>

                                <div class="form__submit">
                                    <button type="submit" class="btn--custom btn--rounded btn--outline @if (!auth('customer')->check()) btn-disabled @endif" @if (!auth('customer')->check()) disabled @endif>{{ __('Submit') }}</button>
                                </div>
                            </div>
                       {!! Form::close() !!}
                    </div>
                    @endif
                </div>
            </div>
        </article>
        @if (theme_option('facebook_comment_enabled_in_product', 'yes') == 'yes')
            <br />
            {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, Theme::partial('comments')) !!}
        @endif

        @php
            $crossSellProducts = get_cross_sale_products($product);
        @endphp
        @if (count($crossSellProducts) > 0)
            <section class="section--related-posts">
                <div class="section__header">
                    <h3>{{ __('Customers who bought this item also bought') }}:</h3>
                </div>
                <div class="section__content">
                    <div class="row">
                        @foreach ($crossSellProducts as $crossSellProduct)
                            <div class="col-lg-3 col-md-4 col-6">
                                {!! Theme::partial('product-item', ['product' => $crossSellProduct]) !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="section--related-posts">
            <div class="section__header">
                <h3>{{ __('Related Products') }}:</h3>
            </div>
            <related-products-component url="{{ route('public.ajax.related-products', $product->id) }}?limit=4"></related-products-component>
        </section>
    </div>
</main>
