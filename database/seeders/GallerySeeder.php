<?php

namespace Database\Seeders;

use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Gallery\Models\Gallery as GalleryModel;
use Botble\Gallery\Models\GalleryMeta;
use Botble\Gallery\Models\GalleryTranslation;
use Botble\Language\Models\LanguageMeta;
use Botble\Slug\Models\Slug;
use Faker\Factory;
use Illuminate\Support\Str;
use SlugHelper;

class GallerySeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('galleries');

        GalleryModel::truncate();
        GalleryMeta::truncate();
        GalleryTranslation::truncate();
        Slug::where('reference_type', GalleryModel::class)->delete();
        MetaBoxModel::where('reference_type', GalleryModel::class)->delete();
        LanguageMeta::where('reference_type', GalleryModel::class)->delete();

        $faker = Factory::create();

        $galleries = [
            [
                'name' => 'Men',
            ],
            [
                'name' => 'Women',
            ],
            [
                'name' => 'Accessories',
            ],
            [
                'name' => 'Shoes',
            ],
            [
                'name' => 'Denim',
            ],
            [
                'name' => 'Dress',
            ],
        ];

        $images = [];
        for ($i = 0; $i < 10; $i++) {
            $images[] = [
                'img'         => 'galleries/' . ($i + 1) . '.jpg',
                'description' => $faker->text(150),
            ];
        }

        foreach ($galleries as $index => $item) {
            $item['description'] = $faker->text(150);
            $item['image'] = 'galleries/' . ($index + 1) . '.jpg';
            $item['user_id'] = 1;
            $item['is_featured'] = true;

            $gallery = GalleryModel::create($item);

            Slug::create([
                'reference_type' => GalleryModel::class,
                'reference_id'   => $gallery->id,
                'key'            => Str::slug($gallery->name),
                'prefix'         => SlugHelper::getPrefix(GalleryModel::class),
            ]);

            GalleryMeta::create([
                'images'         => json_encode($images),
                'reference_id'   => $gallery->id,
                'reference_type' => GalleryModel::class,
            ]);
        }

        $translations = [
            [
                'name' => 'Thời trang nam',
            ],
            [
                'name' => 'Thời trang nữ',
            ],
            [
                'name' => 'Phụ kiện',
            ],
            [
                'name' => 'Giày dép',
            ],
            [
                'name' => 'Denim',
            ],
            [
                'name' => 'Quần áo',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['galleries_id'] = $index + 1;

            GalleryTranslation::insert($item);
        }
    }
}
