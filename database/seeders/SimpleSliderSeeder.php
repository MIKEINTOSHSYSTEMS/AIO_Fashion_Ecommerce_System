<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\SimpleSlider\Models\SimpleSlider;
use Botble\SimpleSlider\Models\SimpleSliderItem;
use Illuminate\Support\Arr;
use MetaBox;

class SimpleSliderSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('sliders');

        SimpleSlider::truncate();
        SimpleSliderItem::truncate();
        LanguageMeta::where('reference_type', SimpleSlider::class)->delete();

        $sliders = [
            'en_US' => [
                [
                    'name' => 'Home slider',
                    'key'  => 'home-slider',
                ],
            ],
            'vi'    => [
                [
                    'name' => 'Slider trang chủ',
                    'key'  => 'slider-trang-chu',
                ],
            ],
        ];

        $sliderItems = [
            'en_US' => [
                [
                    'title'       => 'New Collection',
                    'link'        => '/products',
                    'description' => 'Save more with coupons & up to 70% off',
                    'button_text' => 'Shop now',
                ],
                [
                    'title'       => 'Big Deals',
                    'link'        => '/products',
                    'description' => 'Headphone, Gaming Laptop, PC and more...',
                    'button_text' => 'Discover now',
                ],
                [
                    'title'       => 'Trending Collection',
                    'link'        => '/products',
                    'description' => 'Save more with coupons & up to 20% off',
                    'button_text' => 'Shop now',
                ],
            ],
            'vi'    => [
                [
                    'title'       => 'Bộ sưu tập mới',
                    'link'        => '/products',
                    'description' => 'Tiết kiệm hơn với phiếu giảm giá và giảm giá lên đến 70%',
                    'button_text' => 'Mua ngay',
                ],
                [
                    'title'       => 'Khuyến mãi lớn',
                    'link'        => '/products',
                    'description' => 'Tai nghe, Máy tính xách tay chơi game, PC và hơn thế nữa ...',
                    'button_text' => 'Khám phá ngay',
                ],
                [
                    'title'       => 'Trending Collection',
                    'link'        => '/products',
                    'description' => 'Tiết kiệm hơn với phiếu giảm giá và giảm giá lên đến 20%',
                    'button_text' => 'Mua ngay',
                ],
            ],
        ];

        foreach ($sliders as $locale => $sliderItem) {
            foreach ($sliderItem as $index => $value) {
                $slider = SimpleSlider::create(Arr::only($value, ['name', 'key']));

                $originValue = null;

                if ($locale !== 'en_US') {
                    $originValue = LanguageMeta::where([
                        'reference_id'   => $index + 1,
                        'reference_type' => SimpleSlider::class,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($slider, $locale, $originValue);

                foreach (collect($sliderItems[$locale]) as $key => $item) {
                    $item['image'] = 'sliders/' . ($key + 1) . '.jpg';
                    $item['order'] = $key + 1;
                    $item['simple_slider_id'] = $slider->id;

                    $sliderItem = SimpleSliderItem::create(Arr::except($item, ['button_text']));

                    MetaBox::saveMetaBoxData($sliderItem, 'button_text', $item['button_text']);
                }
            }
        }
    }
}
