<?php

namespace Database\Seeders;

use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\LanguageAdvanced\Models\PageTranslation;
use Botble\Page\Models\Page;
use Botble\Slug\Models\Slug;
use Html;
use Illuminate\Support\Str;
use SlugHelper;

class PageSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages = [
            [
                'name'     => 'Homepage',
                'content'  =>
                    Html::tag('div', '[simple-slider key="home-slider"][/simple-slider]') .
                    Html::tag('div',
                        '[featured-product-categories title="Top Categories" subtitle="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus blandit massa enim Nullam nunc varius."][/featured-product-categories]') .
                    Html::tag('div',
                        '[product-collections title="A change of Season" subtitle="Update your wardrobe with new seasonal trend"][/product-collections]') .
                    Html::tag('div',
                        '[flash-sale title="Deal of the day" subtitle="Best Deals For You" show_popup="yes"][/flash-sale]') .
                    Html::tag('div',
                        '[featured-products title="Our Picks For You" subtitle="Always find the best ways for you" limit="8"][/featured-products]') .
                    Html::tag('div',
                        '[trending-products title="Trending Products" subtitle="Products on trending" limit="4"][/trending-products]') .
                    Html::tag('div', '[featured-brands title="Our Brands"][/featured-brands]') .
                    Html::tag('div',
                        '[news title="Visit Our Blog" subtitle="Our Blog updated the newest trend of the world regularly"][/news]') .
                    Html::tag('div',
                        '[theme-galleries title="@ OUR GALLERIES" subtitle="Our latest fashion galleries images" limit="8"][/theme-galleries]') .
                    Html::tag('div',
                        '[site-features icon1="icon-truck" title1="FREE SHIPPING" subtitle1="Free shipping on all US order or order above $200" icon2="icon-life-buoy" title2="SUPPORT 24/7" subtitle2="Contact us 24 hours a day, 7 days a week" icon3="icon-refresh-ccw" title3="30 DAYS RETURN" subtitle3="Simply return it within 30 days for an exchange" icon4="icon-shield" title4="100% PAYMENT SECURE" subtitle4="We ensure secure payment with PEV"][/site-features]')
                ,
                'template' => 'homepage',
            ],
            [
                'name'    => 'Blog',
                'content' => Html::tag('p',
                    'We always share fashion tips with the hope you guys will find the best style for yourself.<br />Besides, we update the fashion trend as soon as we can.<br />Enjoy your journey!',
                    ['style' => 'text-align: center']),
            ],
            [
                'name'    => 'Contact',
                'content' => Html::tag('p', '[contact-form][/contact-form]'),
            ],
            [
                'name'    => 'Cookie Policy',
                'content' => Html::tag('h3', 'EU Cookie Consent') .
                    Html::tag('p',
                        'To use this website we are using Cookies and collecting some data. To be compliant with the EU GDPR we give you to choose if you allow us to use certain Cookies and to collect some Data.') .
                    Html::tag('h4', 'Essential Data') .
                    Html::tag('p',
                        'The Essential Data is needed to run the Site you are visiting technically. You can not deactivate them.') .
                    Html::tag('p',
                        '- Session Cookie: PHP uses a Cookie to identify user sessions. Without this Cookie the Website is not working.') .
                    Html::tag('p',
                        '- XSRF-Token Cookie: Laravel automatically generates a CSRF "token" for each active user session managed by the application. This token is used to verify that the authenticated user is the one actually making the requests to the application.'),
            ],
            [
                'name'    => 'FAQs',
                'content' => Html::tag('div', '[faqs][/faqs]'),
            ],
        ];

        Page::truncate();
        PageTranslation::truncate();
        Slug::where('reference_type', Page::class)->delete();
        MetaBoxModel::where('reference_type', Page::class)->delete();
        LanguageMeta::where('reference_type', Page::class)->delete();

        foreach ($pages as $item) {
            $item['user_id'] = 1;

            if (!isset($item['template'])) {
                $item['template'] = 'default';
            }

            $page = Page::create($item);

            Slug::create([
                'reference_type' => Page::class,
                'reference_id'   => $page->id,
                'key'            => Str::slug($page->name),
                'prefix'         => SlugHelper::getPrefix(Page::class),
            ]);
        }

        $translations = [
            [
                'name'     => 'Trang chủ',
                'content'  =>
                    Html::tag('div', '[simple-slider key="slider-trang-chu"][/simple-slider]') .
                    Html::tag('div',
                        '[featured-product-categories title="Danh mục nổi bật" subtitle="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus blandit massa enim Nullam nunc varius."][/featured-product-categories]') .
                    Html::tag('div',
                        '[product-collections title="Bộ sưu tập theo mùa" subtitle="Cập nhật tủ quần áo của bạn với xu hướng theo mùa mới"][/product-collections]') .
                    Html::tag('div',
                        '[flash-sale title="Khuyến mãi hôm nay" subtitle="Khuyến mãi tốt nhất dành cho bạn" show_popup="yes"][/flash-sale]') .
                    Html::tag('div',
                        '[featured-products title="Lựa chọn của chúng tôi dành cho bạn" subtitle="Luôn tìm ra những cách tốt nhất cho bạn" limit="8"][/featured-products]') .
                    Html::tag('div',
                        '[trending-products title="Sản phẩm nổi bật" subtitle="Các sản phẩm xu hướng" limit="4"][/trending-products]') .
                    Html::tag('div', '[featured-brands title="Thương hiệu"][/featured-brands]') .
                    Html::tag('div',
                        '[news title="Bài viết mới nhất" subtitle="Blog của chúng tôi cập nhật các xu hướng mới nhất của thế giới thường xuyên"][/news]') .
                    Html::tag('div',
                        '[theme-galleries title="@ Thư viện ảnh" subtitle="Hình ảnh phòng trưng bày thời trang mới nhất của chúng tôi" limit="8"][/theme-galleries]') .
                    Html::tag('div',
                        '[site-features icon1="icon-truck" title1="MIỄN PHÍ VẬN CHUYỂN" subtitle1="Giao hàng miễn phí cho tất cả các đơn đặt hàng tại Hoa Kỳ hoặc đơn hàng trên $200" icon2="icon-life-buoy" title2="HỖ TRỢ 24/7" subtitle2="Liên hệ với chúng tôi 24 giờ một ngày, 7 ngày một tuần" icon3="icon-refresh-ccw" title3="30 HOÀN HÀNG" subtitle3="Hỗ trợ trả hàng trong vòng 30 ngày" icon4="icon-shield" title4="100% THANH TOÁN BẢO MẬT" subtitle4="Chúng tôi đảm bảo thanh toán an toàn với PEV"][/site-features]')
                ,
            ],
            [
                'name'    => 'Tin tức',
                'content' => Html::tag('p',
                    'We always share fashion tips with the hope you guys will find the best style for yourself.<br />Besides, we update the fashion trend as soon as we can.<br />Enjoy your journey!',
                    ['style' => 'text-align: center']),
            ],
            [
                'name'    => 'Liên hệ',
                'content' => Html::tag('p', '[contact-form][/contact-form]'),
            ],
            [
                'name'    => 'Chính sách cookie',
                'content' => Html::tag('h3', 'EU Cookie Consent') .
                    Html::tag('p',
                        'To use this website we are using Cookies and collecting some data. To be compliant with the EU GDPR we give you to choose if you allow us to use certain Cookies and to collect some Data.') .
                    Html::tag('h4', 'Essential Data') .
                    Html::tag('p',
                        'The Essential Data is needed to run the Site you are visiting technically. You can not deactivate them.') .
                    Html::tag('p',
                        '- Session Cookie: PHP uses a Cookie to identify user sessions. Without this Cookie the Website is not working.') .
                    Html::tag('p',
                        '- XSRF-Token Cookie: Laravel automatically generates a CSRF "token" for each active user session managed by the application. This token is used to verify that the authenticated user is the one actually making the requests to the application.'),
            ],
            [
                'name'    => 'Câu hỏi thường gặp',
                'content' => Html::tag('div', '[fas][/fas]'),
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['pages_id'] = $index + 1;

            PageTranslation::insert($item);
        }
    }
}
