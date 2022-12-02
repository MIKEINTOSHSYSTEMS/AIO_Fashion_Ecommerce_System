<?php

namespace Database\Seeders;

use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SlugHelper;

class ProductCategorySeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('product-categories');

        $categories = [
            [
                'name'        => 'Office bags',
                'image'       => 'product-categories/1.png',
                'is_featured' => true,
            ],
            [
                'name'        => 'Hand bag',
                'image'       => 'product-categories/2.png',
                'is_featured' => true,
            ],
            [
                'name'        => 'Woman',
                'image'       => 'product-categories/3.png',
                'is_featured' => true,
                'children'    => [
                    [
                        'name' => 'Woman wallet',
                    ],
                    [
                        'name' => 'Denim',
                    ],
                    [
                        'name' => 'Dress',
                    ],
                ],
            ],
            [
                'name'        => 'Backpack',
                'image'       => 'product-categories/4.png',
                'is_featured' => true,
            ],
            [
                'name'        => 'Bags',
                'image'       => 'product-categories/5.png',
                'is_featured' => true,
            ],
            [
                'name'        => 'Wallet',
                'image'       => 'product-categories/6.png',
                'is_featured' => true,
            ],
            [
                'name'        => 'Men',
                'image'       => 'product-categories/7.png',
                'is_featured' => true,
                'children'    => [
                    [
                        'name' => 'Accessories',
                    ],
                    [
                        'name' => 'Men wallet',
                    ],
                    [
                        'name' => 'Shoes',
                    ],
                ],
            ],
        ];

        ProductCategory::truncate();
        Slug::where('reference_type', ProductCategory::class)->delete();
        MetaBoxModel::where('reference_type', ProductCategory::class)->delete();

        foreach ($categories as $index => $item) {
            $this->createCategoryItem($index, $item);
        }

        // Translations
        DB::table('ec_product_categories_translations')->truncate();

        $translations = [
            [
                'name' => 'Túi xách văn phòng',
            ],
            [
                'name' => 'Túi cầm tay',
            ],
            [
                'name'     => 'Dành cho nữ',
                'children' => [
                    [
                        'name' => 'Ví nữ',
                    ],
                    [
                        'name' => 'Denim',
                    ],
                    [
                        'name' => 'Quần áo',
                    ],
                ],
            ],
            [
                'name' => 'Balo',
            ],
            [
                'name' => 'Túi xách',
            ],
            [
                'name' => 'Ví',
            ],
            [
                'name'     => 'Dành cho nam',
                'children' => [
                    [
                        'name' => 'Phụ kiện',
                    ],
                    [
                        'name' => 'Ví nam',
                    ],
                    [
                        'name' => 'Giày dép',
                    ],
                ],
            ],
        ];

        $count = 1;
        foreach ($translations as $translation) {

            $translation['lang_code'] = 'vi';
            $translation['ec_product_categories_id'] = $count;

            DB::table('ec_product_categories_translations')->insert(Arr::except($translation, ['children']));

            $count++;

            if (isset($translation['children'])) {
                foreach ($translation['children'] as $child) {

                    $child['lang_code'] = 'vi';
                    $child['ec_product_categories_id'] = $count;

                    DB::table('ec_product_categories_translations')->insert($child);

                    $count++;
                }
            }
        }
    }

    /**
     * @param int $index
     * @param array $category
     * @param int $parentId
     */
    protected function createCategoryItem(int $index, array $category, int $parentId = 0): void
    {
        $category['parent_id'] = $parentId;
        $category['order'] = $index + 1;

        if (Arr::has($category, 'children')) {
            $children = $category['children'];
            unset($category['children']);
        } else {
            $children = [];
        }

        $createdCategory = ProductCategory::create($category);

        Slug::create([
            'reference_type' => ProductCategory::class,
            'reference_id'   => $createdCategory->id,
            'key'            => Str::slug($createdCategory->name),
            'prefix'         => SlugHelper::getPrefix(ProductCategory::class),
        ]);

        if ($children) {
            foreach ($children as $childIndex => $child) {
                $this->createCategoryItem($childIndex, $child, $createdCategory->id);
            }
        }
    }
}
