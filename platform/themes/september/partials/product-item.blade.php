@if ($product)
    <div class="product">
        <div class="product__wrapper">
            <div class="product__thumbnail">
                @if ($product->isOutOfStock())
                    <div class="product__badges">
                        <span class="badge" style="background-color: #000">{{ __('Out Of Stock') }}</span>
                    </div>
                @else
                    @if ($product->productLabels->count() || $product->front_sale_price !== $product->price)
                        <div class="product__badges">
                            @foreach ($product->productLabels as $label)
                                <span class="badge" @if ($label->color) style="background-color: {{ $label->color }}" @endif>{{ $label->name }}</span>
                            @endforeach
                            @if ($product->front_sale_price !== $product->price)
                                <span class="badge badge--sale">{{ get_sale_percentage($product->price, $product->front_sale_price) }}</span>
                            @endif
                        </div>
                    @endif
                @endif
                <a class="product__overlay" href="{{ $product->url }}"></a>
                <img src="{{ RvMedia::getImageUrl($product->image, 'medium', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->url }}" />
                @if (EcommerceHelper::isWishlistEnabled())
                    <a class="product__favorite js-add-to-wishlist-button" href="#" data-url="{{ route('public.wishlist.add', $product->id) }}">
                        <i class="fa fa-heart-o"></i>
                    </a>
                @endif
                <ul class="product__actions">
                    @if (EcommerceHelper::isCartEnabled() && !$product->isOutOfStock())
                        <li><a class="add-to-cart-button" data-id="{{ $product->id }}" href="#" data-url="{{ route('public.cart.add-to-cart') }}">{{ __('Add to cart') }}</a></li>
                    @endif
                </ul>
                @if (count($product->variationAttributeSwatchesForProductList))
                    <ul class="product__variants color-swatch">
                        @foreach($product->variationAttributeSwatchesForProductList->unique('attribute_id') as $attribute)
                            @if ($attribute->display_layout == 'visual')
                                <li>
                                    <div class="custom-checkbox">
                                        <span style="{{ $attribute->image ? 'background-image: url(' . RvMedia::getImageUrl($attribute->image) . ');' : 'background-color: ' . $attribute->color . ';' }}; cursor: initial;"></span>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="product__content" data-mh="product-item">
                <a class="product__title" href="{{ $product->url }}">{{ $product->name }}</a>

                {!! apply_filters('ecommerce_before_product_price_in_listing', null, $product) !!}
                <p class="product__price @if ($product->front_sale_price !== $product->price) sale @endif">
                    <span>{{ format_price($product->front_sale_price_with_taxes) }}</span>
                    @if ($product->front_sale_price !== $product->price)
                        <del><span>{{ format_price($product->price_with_taxes) }}</span></del>
                    @endif
                </p>
                {!! apply_filters('ecommerce_after_product_price_in_listing', null, $product) !!}

                @if (EcommerceHelper::isReviewEnabled())
                    <div class="rating_wrap">
                        <div class="rating">
                            <div class="product_rate" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                        </div>
                        <span class="rating_num">({{ $product->reviews_count }})</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
