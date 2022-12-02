<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Widget\Models\Widget as WidgetModel;
use Theme;

class WidgetSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        WidgetModel::truncate();

        $data = [
            'en_US' => [
                [
                    'widget_id'  => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position'   => 1,
                    'data'       => [
                        'id'      => 'CustomMenuWidget',
                        'name'    => 'Customer services',
                        'menu_id' => 'customer-services',
                    ],
                ],
            ],
            'vi'    => [
                [
                    'widget_id'  => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position'   => 1,
                    'data'       => [
                        'id'      => 'CustomMenuWidget',
                        'name'    => 'Dịch vụ khách hàng',
                        'menu_id' => 'dich-vu-khach-hang',
                    ],
                ],
            ],
        ];

        $theme = Theme::getThemeName();

        foreach ($data as $locale => $widgets) {
            foreach ($widgets as $item) {
                $item['theme'] = $locale == 'en_US' ? $theme : ($theme . '-' . $locale);
                WidgetModel::create($item);
            }
        }
    }
}
