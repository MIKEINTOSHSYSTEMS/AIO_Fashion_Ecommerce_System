<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Language\Models\LanguageMeta;
use Botble\Menu\Models\Menu as MenuModel;
use Botble\Menu\Models\MenuLocation;
use Botble\Menu\Models\MenuNode;
use Botble\Page\Models\Page;
use Illuminate\Support\Arr;
use Menu;

class MenuSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'en_US' => [
                [
                    'name'     => 'Main menu',
                    'slug'     => 'main-menu',
                    'location' => 'main-menu',
                    'items'    => [
                        [
                            'title'     => 'Home',
                            'url'       => '/',
                        ],
                        [
                            'title'     => 'Products',
                            'url'       => '/products',
                            'children'     => [
                                [
                                    'title'          => 'Woman wallet',
                                    'reference_id'   => 1,
                                    'reference_type' => ProductCategory::class,
                                ],
                                [
                                    'title'          => 'Office bags',
                                    'reference_id'   => 2,
                                    'reference_type' => ProductCategory::class,
                                ],
                                [
                                    'title'          => 'Hand bag',
                                    'reference_id'   => 3,
                                    'reference_type' => ProductCategory::class,
                                ],
                                [
                                    'title'          => 'Backpack',
                                    'reference_id'   => 4,
                                    'reference_type' => ProductCategory::class,
                                ],
                            ],
                        ],
                        [
                            'title'          => 'Blog',
                            'reference_id'   => 2,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title'          => 'FAQ',
                            'reference_id'   => 5,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title'          => 'Contact',
                            'reference_id'   => 3,
                            'reference_type' => Page::class,
                        ],
                    ],
                ],
                [
                    'name'  => 'Customer services',
                    'slug'  => 'customer-services',
                    'items' => [
                        [
                            'title'     => 'Login',
                            'url'       => '/login',
                        ],
                        [
                            'title'     => 'Register',
                            'url'       => '/register',
                        ],
                        [
                            'title'          => 'Blog',
                            'reference_id'   => 2,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title'          => 'Contact',
                            'reference_id'   => 3,
                            'reference_type' => Page::class,
                        ],
                    ],
                ],
            ],
            'vi'    => [
                [
                    'name'     => 'Menu chính',
                    'slug'     => 'menu-chinh',
                    'location' => 'main-menu',
                    'items'    => [
                        [
                            'title'     => 'Trang chủ',
                            'url'       => '/',
                        ],
                        [
                            'title'     => 'Sản phẩm',
                            'url'       => '/products',
                            'children'     => [
                                [
                                    'title'          => 'Túi xách nữ',
                                    'reference_id'   => 1,
                                    'reference_type' => ProductCategory::class,
                                ],
                                [
                                    'title'          => 'Túi công sở',
                                    'reference_id'   => 2,
                                    'reference_type' => ProductCategory::class,
                                ],
                                [
                                    'title'          => 'Ví cầm tay',
                                    'reference_id'   => 3,
                                    'reference_type' => ProductCategory::class,
                                ],
                                [
                                    'title'          => 'Ba lô',
                                    'reference_id'   => 4,
                                    'reference_type' => ProductCategory::class,
                                ],
                            ],
                        ],
                        [
                            'title'          => 'Tin tức',
                            'reference_id'   => 2,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title'          => 'Liên hệ',
                            'reference_id'   => 3,
                            'reference_type' => Page::class,
                        ],
                    ],
                ],
                [
                    'name'  => 'Dịch vụ khách hàng',
                    'slug'  => 'dich-vu-khach-hang',
                    'items' => [
                        [
                            'title'     => 'Đăng nhập',
                            'url'       => '/login',
                        ],
                        [
                            'title'     => 'Đăng ký',
                            'url'       => '/register',
                        ],
                        [
                            'title'          => 'Tin tức',
                            'reference_id'   => 2,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title'          => 'Liên hệ',
                            'reference_id'   => 3,
                            'reference_type' => Page::class,
                        ],
                    ],
                ],
            ],
        ];

        MenuModel::truncate();
        MenuLocation::truncate();
        MenuNode::truncate();
        LanguageMeta::where('reference_type', MenuModel::class)->delete();
        LanguageMeta::where('reference_type', MenuLocation::class)->delete();

        foreach ($data as $locale => $menus) {
            foreach ($menus as $index => $item) {
                $menu = MenuModel::create(Arr::except($item, ['items', 'location']));

                if (isset($item['location'])) {
                    $menuLocation = MenuLocation::create([
                        'menu_id'  => $menu->id,
                        'location' => $item['location'],
                    ]);

                    $originValue = LanguageMeta::where([
                        'reference_id'   => $locale == 'en_US' ? $menu->id : $menu->id + 3,
                        'reference_type' => MenuLocation::class,
                    ])->value('lang_meta_origin');

                    LanguageMeta::saveMetaData($menuLocation, $locale, $originValue);
                }

                foreach ($item['items'] as $menuNode) {
                    $this->createMenuNode($index, $menuNode, $locale, $menu->id);
                }

                $originValue = null;

                if ($locale !== 'en_US') {
                    $originValue = LanguageMeta::where([
                        'reference_id'   => $index + 1,
                        'reference_type' => MenuModel::class,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($menu, $locale, $originValue);
            }
        }

        Menu::clearCacheMenuItems();
    }

    /**
     * @param int $index
     * @param array $menuNode
     * @param string $locale
     * @param int $menuId
     * @param int $parentId
     */
    protected function createMenuNode(int $index, array $menuNode, string $locale, int $menuId, int $parentId = 0): void
    {
        $menuNode['menu_id'] = $menuId;
        $menuNode['parent_id'] = $parentId;

        if (Arr::has($menuNode, 'children')) {
            $children = $menuNode['children'];
            $menuNode['has_child'] = true;

            unset($menuNode['children']);
        } else {
            $children = [];
            $menuNode['has_child'] = false;
        }

        $createdNode = MenuNode::create($menuNode);

        if ($children) {
            foreach ($children as $child) {
                $this->createMenuNode($index, $child, $locale, $menuId, $createdNode->id);
            }
        }
    }
}
