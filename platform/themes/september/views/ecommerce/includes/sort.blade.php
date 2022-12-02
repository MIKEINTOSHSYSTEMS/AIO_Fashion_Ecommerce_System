<div class="shop__sort">
    <div class="form-group--inline">
        <label for="sort-by">{{ __('Sort by') }}</label>
        <div class="form-group__content">
            <div class="select--arrow">
                <select name="sort-by" id="sort-by" class="form-control">
                    @foreach (EcommerceHelper::getSortParams() as $key => $name)
                        <option value="{{ $key }}" @if (request()->input('sort-by') == $key) selected @endif>{{ $name }}</option>
                    @endforeach
                </select>
                <i class="feather icon icon-chevron-down"></i>
            </div>
        </div>
    </div>
</div>
