@if (function_exists('get_galleries'))
    @php $galleries = get_galleries($limit); @endphp
    @if (!$galleries->isEmpty())
        <section class="section--instagram">
            @if ($title)
                <div class="section__follow-instagram">
                    <figure>
                        <a href="{{ route('public.galleries') }}"><h3>{!! clean($title) !!}</h3></a>
                        @if ($description)
                            <p>{!! clean($description) !!}</p>
                        @endif
                        @if ($subtitle)
                            <p>{!! clean($subtitle) !!}</p>
                        @endif
                    </figure>
                </div>
            @endif
            <div class="instagram-images">
                @foreach ($galleries as $gallery)
                    <div class="block--instagram">
                        <img src="{{ RvMedia::getImageUrl($gallery->image, 'medium') }}" alt="{{ $gallery->name }}">
                        <a href="{{ $gallery->url }}" class="block__overlay"></a>
                        <div class="block__actions">
                            <p class="block__caption">{{ $gallery->name }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        <div class="clearfix"></div>
    @endif
@endif

