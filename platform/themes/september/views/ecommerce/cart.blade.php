<section class="section section--shopping-cart">
    <div class="section__header">
        <h3>{{ __('Shopping Cart') }}</h3>
    </div>
    <div class="section__content">
        @if (Cart::instance('cart')->count() > 0)
            <form class="form--shopping-cart" method="post" action="{{ route('public.cart.update') }}">
                @csrf
                <div class="form__section">
                        <div class="table-responsive">
                            <table class="table table--cart">
                                <thead>
                                <tr>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if (isset($products) && $products)
                                        @foreach(Cart::instance('cart')->content() as $key => $cartItem)
                                            @php
                                                $product = $products->where('id', $cartItem->id)->first();
                                            @endphp

                                            @if (!empty($product))
                                                <tr>
                                                    <td>
                                                        <div class="product--cart">
                                                            <div class="product__thumbnail">
                                                                <a href="{{ $product->original_product->url }}" class="product__overlay">
                                                                    <img src="{{ $cartItem->options['image'] }}" alt="{{ $product->original_product->name }}" />
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="product__content">
                                                            <a href="{{ $product->original_product->url }}" title="{{ $product->original_product->name }}">{{ $product->original_product->name }} @if ($product->isOutOfStock()) <span class="stock-status-label">({!! $product->stock_status_html !!})</span> @endif</a>
                                                            <p style="margin-bottom: 0;">
                                                                <small>{{ $cartItem->options['attributes'] ?? '' }}</small>
                                                            </p>
                                                            @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                                                @foreach($cartItem->options['extras'] as $option)
                                                                    @if (!empty($option['key']) && !empty($option['value']))
                                                                        <p style="margin-bottom: 0;"><small>{{ $option['key'] }}: <strong> {{ $option['value'] }}</strong></small></p>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="product__price @if ($product->front_sale_price != $product->price) sale @endif">
                                                            <span>{{ format_price($cartItem->price) }}</span>
                                                            @if ($product->front_sale_price != $product->price)
                                                                <small><del>{{ format_price($product->price) }}</del></small>
                                                            @endif
                                                        </div>

                                                        <input type="hidden" name="items[{{ $key }}][rowId]" value="{{ $cartItem->rowId }}">
                                                    </td>
                                                    <td>
                                                        <div class="form-group--number product__qty">
                                                            <button type="button" class="up"></button>
                                                            <input class="form-control qty-input" type="number" value="{{ $cartItem->qty }}" name="items[{{ $key }}][values][qty]">
                                                            <button type="button" class="down"></button>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="product__price">{{ format_price($cartItem->price * $cartItem->qty) }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="#" data-url="{{ route('public.cart.remove', $cartItem->rowId) }}" class="btn--remove remove-cart-button"><i class="feather icon icon-trash-2"></i></a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                    <tr class="sub-total">
                                        <td colspan="5">
                                            <h5>{{ __('Sub total') }}</h5>
                                        </td>
                                        <td>
                                            <h5>{{ format_price(Cart::instance('cart')->rawSubTotal()) }}</h5>
                                        </td>
                                    </tr>
                                    @if ($promotionDiscountAmount)
                                        <tr class="sub-total">
                                            <td colspan="5">
                                                <h5>{{ __('Discount promotion') }}</h5>
                                            </td>
                                            <td>
                                                <h5 class="promotion-discount-amount-text">{{ format_price($promotionDiscountAmount) }}</h5>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (EcommerceHelper::isTaxEnabled())
                                        <tr class="sub-total">
                                            <td colspan="5">
                                                <h5>{{ __('Tax') }}</h5>
                                            </td>
                                            <td>
                                                <h5 class="promotion-discount-amount-text">{{ format_price(Cart::instance('cart')->rawTax()) }}</h5>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="total">
                                        <td colspan="5"><strong>{{ __('Total') }}</strong> <br /> <span>({{ __('Shipping fees not included') }})</span></td>
                                        <td class="total__price product-subtotal">
                                            <span class="amount">{{ format_price(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount) }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <div class="form__submit text-right">
                    <button type="submit" class="btn--custom btn--outline btn--rounded" name="checkout">{{ __('Checkout') }}</button>
                </div>
            </form>

            @if (count($crossSellProducts) > 0)
                <section class="section--related-posts" style="border: none; padding-top: 60px;">
                    <div class="section__header text-left" style="padding-bottom: 0">
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
        @else
            <p class="text-center">{{ __('Your cart is empty!') }}</p>
        @endif
    </div>
</section>
