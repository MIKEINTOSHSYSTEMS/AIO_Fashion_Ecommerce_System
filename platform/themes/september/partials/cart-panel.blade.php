<div class="cart--mini">
    <div class="cart__items">
        @if (Cart::instance('cart')->count() > 0)
            @php
                $products = Cart::instance('cart')->products();
            @endphp
            @if (count($products))
                @foreach(Cart::instance('cart')->content() as $key => $cartItem)
                    @php
                        $product = $products->find($cartItem->id);
                    @endphp

                    @if (!empty($product))
                        <article class="product--on-cart">
                            <div class="product__thumbnail"><a href="{{ $product->original_product->url }}"><img src="{{ $cartItem->options['image'] }}" alt="{{ $product->original_product->name }}"></a></div>
                            <div class="product__content">
                                <a class="product__remove remove-cart-item" href="#" data-url="{{ route('public.cart.remove', $cartItem->rowId) }}">
                                    <i class="feather icon icon-x"></i>
                                </a>
                                <a href="{{ $product->original_product->url }}">{{ $product->original_product->name }}  @if ($product->isOutOfStock()) <span class="stock-status-label">({!! $product->stock_status_html !!})</span> @endif</a>
                                <p style="margin-bottom: 0;">
                                    <small style="font-weight: normal; font-size: 85%;">{{ $cartItem->options['attributes'] ?? '' }}</small>
                                </p>
                                @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                    @foreach($cartItem->options['extras'] as $option)
                                        @if (!empty($option['key']) && !empty($option['value']))
                                            <p style="margin-bottom: 0;"><small>{{ $option['key'] }}: <strong> {{ $option['value'] }}</strong></small></p>
                                        @endif
                                    @endforeach
                                @endif
                                <small>
                                    <span class="d-inline-block">{{ $cartItem->qty }} x</span> <span class="cart-price">{{ format_price($cartItem->price) }} @if ($product->front_sale_price != $product->price)
                                            <small><del>{{ format_price($product->price) }}</del></small>
                                        @endif
                                    </span>
                                </small>
                            </div>
                        </article>
                    @endif
                @endforeach
            @endif
        @else
            <p class="text-center">{{ __('Your cart is empty!') }}</p>
        @endif
    </div>
    @if (Cart::instance('cart')->count() > 0)
        <div class="cart__footer">
            <div class="cart__summary">
                @if (EcommerceHelper::isTaxEnabled())
                    <p>{{ __('Sub Total') }}:<span>{{ format_price(Cart::instance('cart')->rawSubTotal()) }}</span></p>
                    <p>{{ __('Tax') }}:<span>{{ format_price(Cart::instance('cart')->rawTax()) }}</span></p>
                    <p><strong>{{ __('Total') }}:</strong><span><strong>{{ format_price(Cart::instance('cart')->rawSubTotal() + Cart::instance('cart')->rawTax()) }}</strong></span></p>
                @else
                    <p><strong>{{ __('Sub Total') }}:</strong><span><strong>{{ format_price(Cart::instance('cart')->rawSubTotal()) }}</strong></span></p>
                @endif
            </div>
            @if (Cart::instance('cart')->rawTotal())
                <div class="cart__actions">
                    @if (session('tracked_start_checkout'))
                        <p><a class="btn--custom btn--rounded" href="{{ route('public.checkout.information', session('tracked_start_checkout')) }}">{{ __('Checkout') }} <i class="feather icon-arrow-right"></i></a></p>
                    @endif
                    <p><a class="btn--custom btn--outline btn--rounded" href="{{ route('public.cart') }}">{{ __('View cart') }} <i class="feather icon-arrow-right"></i></a></p>
                </div>
            @endif
        </div>
    @endif
</div>
