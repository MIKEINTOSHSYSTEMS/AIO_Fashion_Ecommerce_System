<section class="section section--shopping-cart">
    <div class="section__header">
        <h3>{{ __('Wishlist') }}</h3>
    </div>
    <div class="section__content">
        <div class="customer-list-order">
            <table class="table table--orders table--wishlist">
                <thead>
                    <tr>
                        <th>{{ __('Image') }}</th>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if ($products->total())
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <img alt="{{ $product->name }}" width="50" height="70" class="img-fluid"
                                         style="max-height: 75px"
                                         src="{{ RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()) }}">
                                </td>
                                <td><a href="{{ $product->original_product->url }}">{{ $product->name }}</a></td>
                                <td>
                                    <div class="product__price @if ($product->front_sale_price != $product->price) sale @endif">
                                        <span>{{ format_price($product->front_sale_price_with_taxes) }}</span>
                                        @if ($product->front_sale_price != $product->price)
                                            <small><del>{{ format_price($product->price_with_taxes) }}</del></small>
                                        @endif
                                    </div>
                                </td>

                                <td style="width: 300px">
                                    <a class="btn--custom btn--rounded btn--outline btn--sm js-remove-from-wishlist-button" href="#" data-url="{{ route('public.wishlist.remove', $product->id) }}">{{ __('Remove') }}</a>
                                    <a class="btn--custom btn--rounded btn--outline btn--sm add-to-cart-button" data-id="{{ $product->id }}" href="#" data-url="{{ route('public.cart.add-to-cart') }}">{{ __('Add to cart') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">{{ __('No product in wishlist!') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if ($products->total())
            {!! $products->links() !!}
        @endif
    </div>
</section>
