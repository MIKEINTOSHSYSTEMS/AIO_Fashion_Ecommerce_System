<div class="ps-site-features">
    <div class="container">
        <div class="ps-block--features">
            <div class="row ps-col-tiny">
                @for ($i = 1; $i < 5; $i++)
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="ps-block--feature">
                            <div class="ps-block__left"><i class="feather icon {!! clean($shortcode->{'icon' . $i}) !!}"></i></div>
                            <div class="ps-block__right">
                                <p>{!! clean($shortcode->{'title' . $i}) !!}</p>
                                <small>{!! clean($shortcode->{'subtitle' . $i}) !!}</small>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
