<div class="visual-swatches-wrapper widget--colors widget-filter-item" data-type="visual">
    <h4 class="widget__title">{{ __('By') }} {{ $set->title }}</h4>
    <div class="widget__content ps-custom-scrollbar">
        <div class="attribute-values">
            <ul class="visual-swatch color-swatch">
                @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                    <li data-slug="{{ $attribute->slug }}"
                        data-toggle="tooltip"
                        data-placement="top"
                        title="{{ $attribute->title }}">
                        <div class="custom-checkbox">
                            <label>
                                <input class="form-control product-filter-item" type="checkbox" name="attributes[]" value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'checked' : '' }}>
                                <span style="{{ $attribute->getAttributeStyle() }}"></span>
                            </label>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
