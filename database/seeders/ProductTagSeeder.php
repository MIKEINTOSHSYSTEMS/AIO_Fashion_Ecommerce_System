<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\ProductTag;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Str;
use SlugHelper;
use Illuminate\Support\Facades\DB;

class ProductTagSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            [
                'name' => 'Wallet',
            ],
            [
                'name' => 'Bags',
            ],
            [
                'name' => 'Shoes',
            ],
            [
                'name' => 'Clothes',
            ],
            [
                'name' => 'Hand bag',
            ],
        ];

        ProductTag::truncate();
        Slug::where('reference_type', ProductTag::class)->delete();

        foreach ($tags as $key => $item) {
            $tag = ProductTag::create($item);

            Slug::create([
                'reference_type' => ProductTag::class,
                'reference_id'   => $tag->id,
                'key'            => Str::slug($tag->name),
                'prefix'         => SlugHelper::getPrefix(ProductTag::class),
            ]);
        }

        DB::table('ec_product_tags_translations')->truncate();

        $translations = [
            [
                'name' => 'Ví',
            ],
            [
                'name' => 'Ba lô',
            ],
            [
                'name' => 'Giày',
            ],
            [
                'name' => 'Quần áo',
            ],
            [
                'name' => 'Túi xách',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ec_product_tags_id'] = $index + 1;

            DB::table('ec_product_tags_translations')->insert($item);
        }
    }
}
