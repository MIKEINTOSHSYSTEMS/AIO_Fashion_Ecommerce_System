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
                'name'     => 'Trang ch???',
                'content'  =>
                    Html::tag('div', '[simple-slider key="slider-trang-chu"][/simple-slider]') .
                    Html::tag('div',
                        '[featured-product-categories title="Danh m???c n???i b???t" subtitle="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus blandit massa enim Nullam nunc varius."][/featured-product-categories]') .
                    Html::tag('div',
                        '[product-collections title="B??? s??u t???p theo m??a" subtitle="C???p nh???t t??? qu???n ??o c???a b???n v???i xu h?????ng theo m??a m???i"][/product-collections]') .
                    Html::tag('div',
                        '[flash-sale title="Khuy???n m??i h??m nay" subtitle="Khuy???n m??i t???t nh???t d??nh cho b???n" show_popup="yes"][/flash-sale]') .
                    Html::tag('div',
                        '[featured-products title="L???a ch???n c???a ch??ng t??i d??nh cho b???n" subtitle="Lu??n t??m ra nh???ng c??ch t???t nh???t cho b???n" limit="8"][/featured-products]') .
                    Html::tag('div',
                        '[trending-products title="S???n ph???m n???i b???t" subtitle="C??c s???n ph???m xu h?????ng" limit="4"][/trending-products]') .
                    Html::tag('div', '[featured-brands title="Th????ng hi???u"][/featured-brands]') .
                    Html::tag('div',
                        '[news title="B??i vi???t m???i nh???t" subtitle="Blog c???a ch??ng t??i c???p nh???t c??c xu h?????ng m???i nh???t c???a th??? gi???i th?????ng xuy??n"][/news]') .
                    Html::tag('div',
                        '[theme-galleries title="@ Th?? vi???n ???nh" subtitle="H??nh ???nh ph??ng tr??ng b??y th???i trang m???i nh???t c???a ch??ng t??i" limit="8"][/theme-galleries]') .
                    Html::tag('div',
                        '[site-features icon1="icon-truck" title1="MI???N PH?? V???N CHUY???N" subtitle1="Giao h??ng mi???n ph?? cho t???t c??? c??c ????n ?????t h??ng t???i Hoa K??? ho???c ????n h??ng tr??n $200" icon2="icon-life-buoy" title2="H??? TR??? 24/7" subtitle2="Li??n h??? v???i ch??ng t??i 24 gi??? m???t ng??y, 7 ng??y m???t tu???n" icon3="icon-refresh-ccw" title3="30 HO??N H??NG" subtitle3="H??? tr??? tr??? h??ng trong v??ng 30 ng??y" icon4="icon-shield" title4="100% THANH TO??N B???O M???T" subtitle4="Ch??ng t??i ?????m b???o thanh to??n an to??n v???i PEV"][/site-features]')
                ,
            ],
            [
                'name'    => 'Tin t???c',
                'content' => Html::tag('p',
                    'We always share fashion tips with the hope you guys will find the best style for yourself.<br />Besides, we update the fashion trend as soon as we can.<br />Enjoy your journey!',
                    ['style' => 'text-align: center']),
            ],
            [
                'name'    => 'Li??n h???',
                'content' => Html::tag('p', '[contact-form][/contact-form]'),
            ],
            [
                'name'    => 'Ch??nh s??ch cookie',
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
                'name'    => 'C??u h???i th?????ng g???p',
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
