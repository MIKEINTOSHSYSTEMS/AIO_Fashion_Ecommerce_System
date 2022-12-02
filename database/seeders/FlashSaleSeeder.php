<?php

namespace Database\Seeders;

use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\FlashSale;
use Botble\Ecommerce\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use MetaBox;

class FlashSaleSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('flash-sales');

        FlashSale::truncate();
        DB::table('ec_flash_sale_products')->truncate();
        MetaBoxModel::where('reference_type', FlashSale::class)->delete();

        $data = [
            [
                'name' => 'Deal of the Day.',
            ],
            [
                'name' => 'Gadgets & Accessories',
            ],
        ];

        $productIds = Product::where('is_variation', 0)->pluck('id')->all();

        foreach ($data as $index => $item) {
            $item['end_date'] = now()
                ->addDays(rand(15, 50))
                ->addHours(rand(1, 23))
                ->addMinutes(rand(1, 59))
                ->toDateString();

            $flashSale = FlashSale::create($item);

            MetaBox::saveMetaBoxData($flashSale, 'image', 'flash-sales/' . ($index + 1) . '.jpg');

            $product = Product::find(Arr::random($productIds));

            $price = $product->price;

            if ($product->front_sale_price !== $product->price) {
                $price = $product->front_sale_price;
            }

            $flashSale->products()->attach([
                $product->id => [
                    'price'    => $price - ($price * rand(10, 70) / 100),
                    'quantity' => rand(6, 20),
                    'sold'     => rand(1, 5),
                ],
            ]);
        }

        DB::table('ec_flash_sales_translations')->truncate();

        $translations = [
            [
                'name' => 'Khuyến mãi trong ngày.',
            ],
            [
                'name' => 'Tiện ích & Phụ kiện',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ec_flash_sales_id'] = $index + 1;

            DB::table('ec_flash_sales_translations')->insert($item);

            MetaBox::saveMetaBoxData(FlashSale::find($index + 1), 'vi_image', 'flash-sales/' . ($index + 1) . '.jpg');
        }
    }
}
