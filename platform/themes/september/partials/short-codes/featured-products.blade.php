<section class="section--homepage home-products">
    <div class="container">
        <div class="section__header">
            <h3>{!! clean($title) !!}</h3>
            @if ($description)
                <p>{!! clean($description) !!}</p>
            @endif
            @if ($subtitle)
                <p>{!! clean($subtitle) !!}</p>
            @endif
        </div>
        <div class="section__content">
            <featured-products-component url="{{ route('public.ajax.featured-products', ['limit' => $limit]) }}"></featured-products-component>
        </div>
    </div>
</section>
