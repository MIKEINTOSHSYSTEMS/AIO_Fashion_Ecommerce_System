<div class="ps-site-features">
    <div class="container">
        <div class="ps-block--features">
            <div class="row ps-col-tiny">
                @foreach($data as $item)
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="ps-block--feature">
                            <div class="ps-block__left"><i class="{{ Arr::get($item, 'icon') }}"></i></div>
                            <div class="ps-block__right">
                                <p>{{ Arr::get($item, 'title') }}</p>
                                <small>{{ Arr::get($item, 'description', Arr::get($item, 'subtitle')) }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
