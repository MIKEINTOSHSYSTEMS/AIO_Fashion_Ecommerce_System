<section class="section--homepage">
    <div class="container">
        <div class="section__header">
            <h3>{!! clean($title) !!}</h3>
            @if ($subtitle)
                <p>{!! clean($subtitle) !!}</p>
            @endif
        </div>
        <div class="section__content">
            <section class="deals" id="flash-sale-products">
                <div class="container">
                    <flash-sale-products-component url="{{ route('public.ajax.get-flash-sales', ['limit' => $limit]) }}"></flash-sale-products-component>
                </div>
            </section>
        </div>
    </div>
</section>

@if ($flashSale && $showPopup == 'yes' && $flashSale->products->count())
    @php
        $product = $flashSale->products->random();
    @endphp
    <div class="modal fade custom-modal" id="flash-sale-modal" data-id="flash-sale-id-{{ $flashSale->id }}" tabindex="-1" aria-labelledby="onloadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="deal" style="background-image: url({{ RvMedia::getImageUrl($flashSale->getMetaData('image', true), null, false, RvMedia::getDefaultImage()) }});">
                        <div class="deal-content">
                            <h6 class="product-title"><a href="{{ $product->url }}">{{ $product->name }}</a></h6>
                            <div class="product-price">
                                <span class="new-price">{{ format_price($product->front_sale_price_with_taxes) }}</span>
                                @if ($product->front_sale_price !== $product->price)
                                    <span class="old-price">{{ format_price($product->price_with_taxes) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="deal-bottom">
                            <p>{{ __('Hurry Up! Offer End In:') }}</p>
                            <div class="deals-countdown" data-countdown="{{ $flashSale->end_date }}"></div>
                            <a href="{{ $product->url }}" class="btn--custom btn--rounded btn--sm">{{ __('Shop Now') }} <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
