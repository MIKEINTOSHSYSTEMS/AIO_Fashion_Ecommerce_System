<ul {!! $options !!}>
    @foreach ($menu_nodes as $key => $row)
        <li class="menu-item @if ($row->has_child) menu-item-has-children @endif {{ $row->css_class }} @if ($row->active) current-menu-item @endif">
            <a href="{{ url($row->url) }}" target="{{ $row->target }}">
                @if ($row->icon_font)<i class='{{ trim($row->icon_font) }}'></i> @endif{{ $row->title }}
            </a>
            @if ($row->has_child)
                <span class="feather icon icon-chevron-down sub-toggle-desktop"></span>
                <span class="sub-toggle"></span>
                {!!
                    Menu::generateMenu([
                        'menu'       => $menu,
                        'view'       => 'main-menu',
                        'options'    => ['class' => 'sub-menu'],
                        'menu_nodes' => $row->child,
                    ])
                !!}
            @endif
        </li>
    @endforeach

    @if (Str::contains($options, 'class="menu menu--mobile"'))
        <li>
            @if (is_plugin_active('language'))
                <div class="language-wrapper" style="padding: 10px 20px 10px 0;">
                    {!! Theme::partial('language-switcher') !!}
                </div>
            @endif
        </li>
        <li>
            @if (is_plugin_active('ecommerce'))
                @php $currencies = get_all_currencies(); @endphp
                @if (count($currencies) > 1)
                    <div class="language-wrapper choose-currency" style="padding: 10px 20px 10px 0;">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle btn-select-language" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                {{ get_application_currency()->title }}
                                <span class="feather icon icon-chevron-down"></span>
                            </button>
                            <ul class="dropdown-menu language_bar_chooser">
                                @foreach ($currencies as $currency)
                                    <li>
                                        <a href="{{ route('public.change-currency', $currency->title) }}" @if (get_application_currency_id() == $currency->id) class="active" @endif><span>{{ $currency->title }}</span></a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            @endif
        </li>
    @endif
</ul>
