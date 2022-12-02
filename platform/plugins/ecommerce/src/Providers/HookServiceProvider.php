<?php

namespace Botble\Ecommerce\Providers;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\Ecommerce\Facades\DiscountFacade;
use Botble\Ecommerce\Facades\FlashSaleFacade;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\FlashSale;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ReviewInterface;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Theme\Supports\ThemeSupport;
use EcommerceHelper;
use File;
use Form;
use Html;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Menu;
use MetaBox;
use OrderHelper;
use Route;
use RvMedia;
use Theme;
use Throwable;

class HookServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if (defined('MENU_ACTION_SIDEBAR_OPTIONS')) {
            Menu::addMenuOptionModel(Brand::class);
            Menu::addMenuOptionModel(ProductCategory::class);
            add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 12);
        }

        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 208, 2);

        if (function_exists('theme_option')) {
            add_action(RENDERING_THEME_OPTIONS_PAGE, [$this, 'addThemeOptions'], 35);
        }

        add_filter(BASE_FILTER_TOP_HEADER_LAYOUT, [$this, 'registerTopHeaderNotification'], 121);
        add_filter(BASE_FILTER_APPEND_MENU_NAME, [$this, 'getPendingOrders'], 130, 2);
        add_filter(BASE_FILTER_MENU_ITEMS_COUNT, [$this, 'getMenuItemCount'], 120);

        add_filter(RENDER_PRODUCTS_IN_CHECKOUT_PAGE, [$this, 'renderProductsInCheckoutPage'], 1000);

        $this->app->booted(function () {
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, function ($widgets) {
                foreach ($widgets as $key => $widget) {
                    if (in_array($key, [
                            'widget_total_themes',
                            'widget_total_users',
                            'widget_total_plugins',
                            'widget_total_pages',
                        ]) && $widget['type'] == 'stats') {
                        Arr::forget($widgets, $key);
                    }
                }

                return $widgets;
            }, 150);

            add_filter(DASHBOARD_FILTER_ADMIN_LIST, function ($widgets, $widgetSettings) {
                $items = app(OrderInterface::class)->count(['is_finished' => 1]);
                return (new DashboardWidgetInstance)
                    ->setType('stats')
                    ->setPermission('orders.index')
                    ->setTitle(trans('plugins/ecommerce::order.menu'))
                    ->setKey('widget_total_1')
                    ->setIcon('fas fa-users')
                    ->setColor('#32c5d2')
                    ->setStatsTotal($items)
                    ->setRoute(route('orders.index'))
                    ->init($widgets, $widgetSettings);
            }, 2, 2);

            add_filter(DASHBOARD_FILTER_ADMIN_LIST, function ($widgets, $widgetSettings) {
                $items = app(ProductInterface::class)->count([
                    'status'       => BaseStatusEnum::PUBLISHED,
                    'is_variation' => 0,
                ]);

                return (new DashboardWidgetInstance)
                    ->setType('stats')
                    ->setPermission('products.index')
                    ->setTitle(trans('plugins/ecommerce::products.name'))
                    ->setKey('widget_total_2')
                    ->setIcon('far fa-file-alt')
                    ->setColor('#1280f5')
                    ->setStatsTotal($items)
                    ->setRoute(route('products.index'))
                    ->init($widgets, $widgetSettings);
            }, 3, 2);

            add_filter(DASHBOARD_FILTER_ADMIN_LIST, function ($widgets, $widgetSettings) {
                $items = app(CustomerInterface::class)->count();
                return (new DashboardWidgetInstance)
                    ->setType('stats')
                    ->setPermission('customers.index')
                    ->setTitle(trans('plugins/ecommerce::customer.name'))
                    ->setKey('widget_total_3')
                    ->setIcon('fas fa-users')
                    ->setColor('#75b6f9')
                    ->setStatsTotal($items)
                    ->setRoute(route('customers.index'))
                    ->init($widgets, $widgetSettings);
            }, 4, 2);

            add_filter(DASHBOARD_FILTER_ADMIN_LIST, function ($widgets, $widgetSettings) {
                $items = app(ReviewInterface::class)->count(['status' => BaseStatusEnum::PUBLISHED]);
                return (new DashboardWidgetInstance)
                    ->setType('stats')
                    ->setPermission('reviews.index')
                    ->setTitle(trans('plugins/ecommerce::review.name'))
                    ->setKey('widget_total_4')
                    ->setIcon('far fa-file-alt')
                    ->setColor('#074f9d')
                    ->setStatsTotal($items)
                    ->setRoute(route('reviews.index'))
                    ->init($widgets, $widgetSettings);
            }, 5, 2);

            if (defined('PAYMENT_FILTER_PAYMENT_PARAMETERS')) {
                add_filter(PAYMENT_FILTER_PAYMENT_PARAMETERS, function ($html) {
                    if (!auth('customer')->check()) {
                        return $html;
                    }

                    return $html . Form::hidden('customer_id', auth('customer')->id())
                            ->toHtml() . Form::hidden('customer_type', Customer::class)->toHtml();
                }, 123);
            }

            if (defined('PAYMENT_FILTER_REDIRECT_URL')) {
                add_filter(PAYMENT_FILTER_REDIRECT_URL, function ($checkoutToken) {
                    return route('public.checkout.success', $checkoutToken ?: OrderHelper::getOrderSessionToken());
                }, 123);
            }

            if (defined('PAYMENT_FILTER_CANCEL_URL')) {
                add_filter(PAYMENT_FILTER_CANCEL_URL, function ($checkoutToken) {
                    return route('public.checkout.information', $checkoutToken ?: OrderHelper::getOrderSessionToken());
                }, 123);
            }

            if (defined('PAYMENT_ACTION_PAYMENT_PROCESSED')) {
                add_action(PAYMENT_ACTION_PAYMENT_PROCESSED, function ($data) {

                    $orderIds = (array)$data['order_id'];

                    if ($orderIds) {
                        $orders = $this->app->make(OrderInterface::class)->allBy([['id', 'IN', $orderIds]]);
                        foreach ($orders as $order) {
                            $data['amount'] = $order->amount;
                            $data['order_id'] = $order->id;
                            $data['currency'] = strtoupper(cms_currency()->getDefaultCurrency()->title);

                            PaymentHelper::storeLocalPayment($data);
                        }
                    }

                    return OrderHelper::processOrder($orderIds, $data['charge_id']);
                }, 123);
            }

            if (defined('PAYMENT_METHOD_SETTINGS_CONTENT')) {
                add_filter(PAYMENT_METHOD_SETTINGS_CONTENT, function ($html, $paymentMethod) {

                    if ($paymentMethod == PaymentMethodEnum::COD) {
                        return $html . view('plugins/ecommerce::settings.additional-cod-settings')->render();
                    }
                    return $html;
                }, 123, 2);
            }

            if (config('packages.theme.general.enable_custom_js')) {
                add_filter('ecommerce_checkout_header', function ($html) {
                    $customCSSFile = public_path(Theme::path() . '/css/style.integration.css');
                    if (File::exists($customCSSFile)) {
                        $html .= Html::style(Theme::asset()
                            ->url('css/style.integration.css?v=' . filectime($customCSSFile)));
                    }

                    return $html . ThemeSupport::getCustomJS('header');
                }, 15);

                if (setting('custom_body_js')) {
                    add_filter('ecommerce_checkout_body', function ($html) {
                        return $html . ThemeSupport::getCustomJS('body');
                    }, 15);
                }

                if (setting('custom_footer_js')) {
                    add_filter('ecommerce_checkout_footer', function ($html) {
                        return $html . ThemeSupport::getCustomJS('footer');
                    }, 15);
                }
            }

            if (defined('FAQ_MODULE_SCREEN_NAME') && config('plugins.ecommerce.general.enable_faq_in_product_details',
                    false)) {
                add_action(BASE_ACTION_META_BOXES, function ($context, $object) {
                    if (!$object || $context != 'advanced') {
                        return false;
                    }

                    if (!is_in_admin() || get_class($object) != Product::class) {
                        return false;
                    }

                    if (!in_array(Route::currentRouteName(), ['products.create', 'products.edit', 'marketplace.vendor.products.create', 'marketplace.vendor.products.edit'])) {
                        return false;
                    }

                    Assets::addStylesDirectly(['vendor/core/plugins/faq/css/faq.css'])
                        ->addScriptsDirectly(['vendor/core/plugins/faq/js/faq.js']);

                    MetaBox::addMetaBox(
                        'faq_schema_config_wrapper', __('Product FAQs'),
                        function () {
                            $value = [];

                            $args = func_get_args();
                            if ($args[0] && $args[0]->id) {
                                $value = MetaBox::getMetaData($args[0], 'faq_schema_config', true);
                            }

                            $hasValue = !empty($value);

                            $value = json_encode((array)$value);

                            return view('plugins/faq::schema-config-box', compact('value', 'hasValue'))->render();
                        },
                        get_class($object),
                        $context
                    );

                    return true;
                }, 139, 2);
            }

            add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, function ($screen, $object) {
                add_filter(THEME_FRONT_HEADER, function ($html) use ($object) {

                    if (!defined('FAQ_MODULE_SCREEN_NAME') ||
                        get_class($object) != Product::class ||
                        !config('plugins.ecommerce.general.enable_faq_in_product_details', false)
                    ) {
                        return $html;
                    }

                    $value = MetaBox::getMetaData($object, 'faq_schema_config', true);

                    if (!$value || !is_array($value)) {
                        return $html;
                    }

                    if (!empty($value)) {
                        foreach ($value as $key => $item) {
                            if (!$item[0]['value'] && !$item[1]['value']) {
                                Arr::forget($value, $key);
                            }
                        }
                    }

                    $schema = [
                        '@context'   => 'https://schema.org',
                        '@type'      => 'FAQPage',
                        'mainEntity' => [],
                    ];

                    foreach ($value as $item) {
                        $schema['mainEntity'][] = [
                            '@type'          => 'Question',
                            'name'           => clean($item[0]['value']),
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text'  => clean($item[1]['value']),
                            ],
                        ];
                    }

                    $schema = json_encode($schema);

                    return $html . Html::tag('script', $schema, ['type' => 'application/ld+json'])->toHtml();
                }, 139);

                add_filter(THEME_FRONT_HEADER, function ($html) use ($object) {
                    if (get_class($object) != Product::class) {
                        return $html;
                    }

                    $schema = [
                        '@context'    => 'http://schema.org',
                        '@type'       => 'Product',
                        'category'    => implode(', ', $object->categories->pluck('name')->all()),
                        'url'         => $object->url,
                        'description' => clean(strip_tags($object->description)),
                        'name'        => clean($object->name),
                        'image'       => RvMedia::getImageUrl($object->image, null, false, RvMedia::getDefaultImage()),
                        'brand'       => [
                            '@type' => 'Brand',
                            'name'  => $object->brand->name,
                        ],
                        'sku'         => $object->sku,
                        'offers'      => [
                            '@type'           => 'Offer',
                            'price'           => format_price($object->front_sale_price),
                            'priceCurrency'   => strtoupper(cms_currency()->getDefaultCurrency()->title),
                            'priceValidUntil' => now()->addDay()->toIso8601String(),
                            'itemCondition'   => 'http://schema.org/NewCondition',
                            'availability'    => $object->isOutOfStock() ? 'http://schema.org/OutOfStock' : 'http://schema.org/InStock',
                            'url'             => $object->url,
                        ],
                    ];

                    if (EcommerceHelper::isReviewEnabled()) {
                        $schema['aggregateRating'] = [
                            '@type'       => 'AggregateRating',
                            'ratingValue' => number_format($object->reviews_avg, 2) ?: '5.00',
                            'reviewCount' => $object->reviews_count,
                        ];

                        $bestRating = $object->reviews->sortByDesc('star')->first();

                        if ($bestRating) {
                            $schema['review'] = [
                                '@type'        => 'Review',
                                'reviewRating' => [
                                    '@type'       => 'Rating',
                                    'ratingValue' => number_format($object->reviews_avg, 2) ?: '5.00',
                                    'bestRating'  => $bestRating->star,
                                ],
                                'author' => [
                                    '@type' => 'Person',
                                    'name'  => $bestRating->user_name,
                                ],
                            ];
                        }
                    }

                    $schema = json_encode($schema);

                    return $html . Html::tag('script', $schema, ['type' => 'application/ld+json'])->toHtml();
                });

            }, 139, 2);
        });

        add_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, function ($request, $data = null) {
            if (!$data instanceof Product || !in_array(Route::currentRouteName(), ['products.edit'])) {
                return false;
            }

            $flashSale = null;

            $flashSalePrice = $data->getFlashSalePrice();

            if ($flashSalePrice != $data->price) {
                $flashSale = FlashSaleFacade::getFacadeRoot()->flashSaleForProduct($data);

                if ($flashSale) {
                    $flashSale = FlashSale::find($flashSale->pivot->flash_sale_id);
                }
            }

            $discount = null;

            $discountPrice = $data->getDiscountPrice();

            if ($discountPrice != $data->price) {

                if ($discountPrice < $flashSalePrice) {
                    $flashSale = null;

                    if (!$data->is_variation) {
                        $productCollections = $data->productCollections;
                    } else {
                        $productCollections = $data->original_product->productCollections;
                    }

                    $discount = DiscountFacade::getFacadeRoot()
                        ->promotionForProduct([$data->id], $productCollections->pluck('id')->all());
                }
            }

            if ($flashSale || $discount) {
                echo view('plugins/ecommerce::products.partials.product-price-warning', compact('flashSale', 'discount', 'data'))
                    ->render();
            }

            return true;
        }
        , 145, 2);
    }

    public function addThemeOptions()
    {
        theme_option()
            ->setSection([
                'title'      => trans('plugins/ecommerce::ecommerce.theme_options.name'),
                'desc'       => trans('plugins/ecommerce::ecommerce.theme_options.description'),
                'id'         => 'opt-text-subsection-ecommerce',
                'subsection' => true,
                'icon'       => 'fa fa-shopping-cart',
                'fields'     => [
                    [
                        'id'         => 'number_of_products_per_page',
                        'type'       => 'number',
                        'label'      => trans('plugins/ecommerce::ecommerce.theme_options.number_products_per_page'),
                        'attributes' => [
                            'name'    => 'number_of_products_per_page',
                            'value'   => 12,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id'         => 'number_of_cross_sale_product',
                        'type'       => 'number',
                        'label'      => trans('plugins/ecommerce::ecommerce.theme_options.number_of_cross_sale_product'),
                        'attributes' => [
                            'name'    => 'number_of_cross_sale_product',
                            'value'   => 4,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id'         => 'max_filter_price',
                        'type'       => 'number',
                        'label'      => trans('plugins/ecommerce::ecommerce.theme_options.max_price_filter'),
                        'attributes' => [
                            'name'    => 'max_filter_price',
                            'value'   => 100000,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id'         => 'logo_in_the_checkout_page',
                        'type'       => 'mediaImage',
                        'label'      => trans('plugins/ecommerce::ecommerce.theme_options.logo_in_the_checkout_page'),
                        'attributes' => [
                            'name'  => 'logo_in_the_checkout_page',
                            'value' => null,
                        ],
                    ],
                    [
                        'id'         => 'logo_in_invoices',
                        'type'       => 'mediaImage',
                        'label'      => trans('plugins/ecommerce::ecommerce.theme_options.logo_in_invoices'),
                        'attributes' => [
                            'name'  => 'logo_in_invoices',
                            'value' => null,
                        ],
                    ],
                ],
            ]);
    }

    /**
     * Register sidebar options in menu
     *
     * @throws Throwable
     */
    public function registerMenuOptions()
    {
        if (Auth::user()->hasPermission('brands.index')) {
            Menu::registerMenuOptions(Brand::class, trans('plugins/ecommerce::brands.menu'));
        }

        if (Auth::user()->hasPermission('product-categories.index')) {
            Menu::registerMenuOptions(ProductCategory::class, trans('plugins/ecommerce::product-categories.menu'));
        }

        return true;
    }

    /**
     * @param array $widgets
     * @param Collection $widgetSettings
     * @return array
     * @throws Throwable
     */
    public function registerDashboardWidgets($widgets, $widgetSettings)
    {
        if (!Auth::user()->hasPermission('ecommerce.report.index')) {
            return $widgets;
        }

        Assets::addScriptsDirectly(['vendor/core/plugins/ecommerce/js/dashboard-widgets.js']);

        return (new DashboardWidgetInstance)
            ->setPermission('ecommerce.report.index')
            ->setKey('widget_ecommerce_report_general')
            ->setTitle(trans('plugins/ecommerce::ecommerce.name'))
            ->setIcon('fas fa-shopping-basket')
            ->setColor('#7ad03a')
            ->setRoute(route('ecommerce.report.dashboard-widget.general'))
            ->setBodyClass('scroll-table')
            ->setColumn('col-md-6 col-sm-6')
            ->init($widgets, $widgetSettings);
    }

    /**
     * @param string $options
     * @return string
     *
     * @throws Throwable
     */
    public function registerTopHeaderNotification($options)
    {
        if (Auth::user()->hasPermission('orders.edit')) {
            $orders = $this->app->make(OrderInterface::class)->advancedGet([
                'condition' => [
                    'status'      => BaseStatusEnum::PENDING,
                    'is_finished' => 1,
                ],
                'with'      => ['address', 'user'],
                'paginate'  => [
                    'per_page'      => 10,
                    'current_paged' => 1,
                ],
                'order_by'  => ['created_at' => 'DESC'],
            ]);

            if ($orders->count() == 0) {
                return $options;
            }

            return $options . view('plugins/ecommerce::orders.notification', compact('orders'))->render();
        }

        return $options;
    }

    /**
     * @param int $number
     * @param string $menuId
     * @return string
     * @throws BindingResolutionException
     */
    public function getPendingOrders($number, $menuId)
    {
        switch ($menuId) {
            case 'cms-plugins-ecommerce-order':

                if (!Auth::user()->hasPermission('orders.index')) {
                    return $number;
                }

                $attributes = [
                    'class' => 'badge badge-success menu-item-count pending-orders',
                    'style' => 'display: none;',
                ];

                return Html::tag('span', '', $attributes)->toHtml();

            case 'cms-plugins-ecommerce':

                if (!Auth::user()->hasPermission('orders.index')) {
                    return $number;
                }

                $attributes = [
                    'class' => 'badge badge-success menu-item-count ecommerce-count',
                    'style' => 'display: none;',
                ];

                return Html::tag('span', '', $attributes)->toHtml();
        }

        return $number;
    }

    /**
     * @param array $data
     * @return array
     */
    public function getMenuItemCount(array $data = []): array
    {
        if (Auth::check() && Auth::user()->hasPermission('orders.index')) {

            $pendingOrders = app(OrderInterface::class)->count([
                'status'      => BaseStatusEnum::PENDING,
                'is_finished' => 1,
            ]);

            $data[] = [
                'key'   => 'pending-orders',
                'value' => $pendingOrders,
            ];

            $data[] = [
                'key'   => 'ecommerce-count',
                'value' => $pendingOrders,
            ];
        }

        return $data;
    }

    /**
     * @param Collection|string $products
     * @return string
     */
    public function renderProductsInCheckoutPage($products)
    {
        if ($products instanceof Collection) {
            return view('plugins/ecommerce::orders.checkout.products', compact('products'))->render();
        }

        return $products;
    }
}
