<div class="form-group">
    <label class="control-label">{{ __('Title') }}</label>
    <input type="text" name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" placeholder="Title">
</div>

<div class="form-group">
    <label class="control-label">{{ __('Subtitle') }}</label>
    <textarea name="subtitle" class="form-control" placeholder="{{ __('Subtitle') }}" rows="3">{{ Arr::get($attributes, 'subtitle') }}</textarea>
</div>

<div class="form-group">
    <label class="control-label">{{ trans('plugins/gallery::gallery.shortcode_name') }}</label>
    <input type="number" name="limit" class="form-control" value="{{ Arr::get($attributes, 'limit', 8) }}" placeholder="{{ trans('plugins/gallery::gallery.limit_display') }}">
</div>
