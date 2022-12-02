<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\FlashSaleInterface;
use Botble\Faq\Repositories\Interfaces\FaqCategoryInterface;
use Botble\Theme\Supports\ThemeSupport;

app()->booted(function () {

    ThemeSupport::registerGoogleMapsShortcode();
    ThemeSupport::registerYoutubeShortcode();

    if (is_plugin_active('ecommerce')) {
        add_shortcode('product-categories', __('Product categories'), __('Product categories'), function ($shortcode) {
            return Theme::partial('short-codes.product-categories', [
                'title'       => $shortcode->title,
                'description' => $shortcode->description,
                'subtitle'    => $shortcode->subtitle,
            ]);
        });

        shortcode()->setAdminConfig('product-categories', function ($attributes) {
            return Theme::partial('short-codes.product-categories-admin-config', compact('attributes'));
        });

        add_shortcode('featured-products', __('Featured products'), __('Featured products'), function ($shortcode) {

            return Theme::partial('short-codes.featured-products', [
                'title'       => $shortcode->title,
                'description' => $shortcode->description,
                'subtitle'    => $shortcode->subtitle,
                'limit'       => $shortcode->limit ?: 8,
            ]);
        });

        shortcode()->setAdminConfig('featured-products', function ($attributes) {
            return Theme::partial('short-codes.featured-products-admin-config', compact('attributes'));
        });

        add_shortcode('featured-product-categories', __('Featured Product Categories'),
            __('Featured Product Categories'),
            function ($shortcode) {

                return Theme::partial('short-codes.featured-product-categories', [
                    'title'       => $shortcode->title,
                    'description' => $shortcode->description,
                    'subtitle'    => $shortcode->subtitle,
                ]);
            });

        shortcode()->setAdminConfig('featured-product-categories', function ($attributes) {
            return Theme::partial('short-codes.featured-product-categories-admin-config', compact('attributes'));
        });

        add_shortcode('featured-brands', __('Featured Brands'), __('Featured Brands'), function ($shortcode) {
            return Theme::partial('short-codes.featured-brands', [
                'title' => $shortcode->title,
            ]);
        });

        shortcode()->setAdminConfig('featured-brands', function ($attributes) {
            return Theme::partial('short-codes.featured-brands-admin-config', compact('attributes'));
        });

        add_shortcode('product-collections', __('Product collections'), __('Product collections'),
            function ($shortcode) {
                return Theme::partial('short-codes.product-collections', [
                    'title'       => $shortcode->title,
                    'description' => $shortcode->description,
                    'subtitle'    => $shortcode->subtitle,
                ]);
            });

        shortcode()->setAdminConfig('product-collections', function ($attributes) {
            return Theme::partial('short-codes.product-collections-admin-config', compact('attributes'));
        });

        add_shortcode('trending-products', __('Trending Products'), __('Trending Products'), function ($shortcode) {
            return Theme::partial('short-codes.trending-products', [
                'title'       => $shortcode->title,
                'description' => $shortcode->description,
                'subtitle'    => $shortcode->subtitle,
                'limit'       => $shortcode->limit ?: 4,
            ]);
        });

        shortcode()->setAdminConfig('trending-products', function ($attributes) {
            return Theme::partial('short-codes.trending-products-admin-config', compact('attributes'));
        });

        add_shortcode('all-products', __('All Products'), __('All Products'), function ($shortcode) {

            $products = get_products([
                'paginate'  => [
                    'per_page'      => (int)($shortcode->per_page ?: 12),
                    'current_paged' => (int)request()->input('page'),
                ],
                'withCount' => EcommerceHelper::withReviewsCount(),
            ]);

            return Theme::partial('short-codes.all-products', [
                'title'    => $shortcode->title,
                'subtitle' => $shortcode->subtitle,
                'products' => $products,
            ]);
        });

        shortcode()->setAdminConfig('all-products', function ($attributes) {
            return Theme::partial('short-codes.all-products-admin-config', compact('attributes'));
        });

        add_shortcode('all-brands', __('All Brands'), __('All Brands'), function ($shortcode) {
            $brands = get_all_brands();

            return Theme::partial('short-codes.all-brands', [
                'title'    => $shortcode->title,
                'subtitle' => $shortcode->subtitle,
                'brands'   => $brands,
            ]);
        });

        shortcode()->setAdminConfig('all-brands', function ($attributes) {
            return Theme::partial('short-codes.all-brands-admin-config', compact('attributes'));
        });

        add_shortcode('flash-sale', __('Flash sale'), __('Flash sale'), function ($shortcode) {
            $flashSale = app(FlashSaleInterface::class)
                ->getModel()
                ->notExpired()
                ->with(['products'])
                ->first();

            if (!$flashSale || !$flashSale->products->count()) {
                return null;
            }

            return Theme::partial('short-codes.flash-sale', [
                'title'     => $shortcode->title,
                'subtitle'  => $shortcode->subtitle,
                'showPopup' => $shortcode->show_popup,
                'limit'     => $shortcode->limit ?: 2,
                'flashSale' => $flashSale,
            ]);
        });

        shortcode()->setAdminConfig('flash-sale', function ($attributes) {
            return Theme::partial('short-codes.flash-sale-admin-config', compact('attributes'));
        });
    }

    if (is_plugin_active('blog')) {
        add_shortcode('news', __('News'), __('News'), function ($shortcode) {
            return Theme::partial('short-codes.news', [
                'title'       => $shortcode->title,
                'description' => $shortcode->description,
                'subtitle'    => $shortcode->subtitle,
            ]);
        });
        shortcode()->setAdminConfig('news', function ($attributes) {
            return Theme::partial('short-codes.news-admin-config', compact('attributes'));
        });
    }

    if (is_plugin_active('contact')) {
        add_filter(CONTACT_FORM_TEMPLATE_VIEW, function () {
            return Theme::getThemeNamespace() . '::partials.short-codes.contact-form';
        }, 120);
    }

    if (is_plugin_active('simple-slider')) {
        add_filter(SIMPLE_SLIDER_VIEW_TEMPLATE, function () {
            return Theme::getThemeNamespace() . '::partials.short-codes.sliders';
        }, 120);
    }

    add_shortcode('our-features', __('Our features (deprecated)'), __('Our features (deprecated)'),
        function ($shortcode) {
            $items = $shortcode->items;
            $items = explode(';', $items);
            $data = [];
            foreach ($items as $item) {
                $data[] = json_decode(trim($item), true);
            }

            return Theme::partial('short-codes.our-features', compact('data'));
        });

    add_shortcode('site-features', __('Site features'), __('Site features'), function ($shortcode) {
        return Theme::partial('short-codes.site-features', compact('shortcode'));
    });

    shortcode()->setAdminConfig('site-features', function ($attributes) {
        return Theme::partial('short-codes.site-features-admin-config', compact('attributes'));
    });

    if (is_plugin_active('gallery')) {
        add_shortcode('theme-galleries', __('Galleries (HASA theme)'), __('Galleries images'), function ($shortcode) {
            return Theme::partial('short-codes.galleries', [
                'title'       => $shortcode->title,
                'description' => $shortcode->description,
                'subtitle'    => $shortcode->subtitle,
                'limit'       => (int)$shortcode->limit ?: 8,
            ]);
        });

        shortcode()->setAdminConfig('theme-galleries', function ($attributes) {
            return Theme::partial('short-codes.galleries-admin-config', compact('attributes'));
        });
    }

    if (is_plugin_active('faq')) {
        add_shortcode('faqs', __('FAQs'), __('List of FAQs'), function ($shortcode) {

            $params = [
                'condition' => [
                    'status' => BaseStatusEnum::PUBLISHED,
                ],
                'with'      => [
                    'faqs' => function ($query) {
                        $query->where('status', BaseStatusEnum::PUBLISHED);
                    },
                ],
                'order_by'  => [
                    'faq_categories.order'      => 'ASC',
                    'faq_categories.created_at' => 'DESC',
                ],
            ];

            if ($shortcode->category_id) {
                $params['condition']['id'] = $shortcode->category_id;
            }

            $categories = app(FaqCategoryInterface::class)->advancedGet($params);

            return Theme::partial('short-codes.faqs', compact('categories'));
        });

        shortcode()->setAdminConfig('faqs', function ($attributes) {
            $categories = app(FaqCategoryInterface::class)->pluck('name', 'id', ['status' => BaseStatusEnum::PUBLISHED]);

            return Theme::partial('short-codes.faqs-admin-config', compact('attributes', 'categories'));
        });
    }
});
