<?php

use Botble\Ecommerce\Models\FlashSale;
use Botble\SimpleSlider\Models\SimpleSliderItem;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;

register_page_template([
    'homepage'   => __('Homepage'),
    'full-width' => __('Full width'),
]);

register_sidebar([
    'id'          => 'footer_sidebar',
    'name'        => __('Footer sidebar'),
    'description' => __('Footer sidebar'),
]);

app()->booted(function () {
    remove_sidebar('primary_sidebar');
});

RvMedia::setUploadPathAndURLToPublic();

RvMedia::addSize('medium', 570, 570)
    ->addSize('small', 570, 268);

Form::component('themeIcon', Theme::getThemeNamespace() . '::partials.icons-field', [
    'name',
    'value'      => null,
    'attributes' => [],
]);

if (is_plugin_active('simple-slider')) {
    add_filter(BASE_FILTER_BEFORE_RENDER_FORM, function ($form, $data) {
        if (get_class($data) == SimpleSliderItem::class) {

            $value = MetaBox::getMetaData($data, 'button_text', true);

            $form
                ->addAfter('link', 'button_text', 'text', [
                    'label'      => __('Button text'),
                    'label_attr' => ['class' => 'control-label'],
                    'value'      => $value,
                    'attr'       => [
                        'placeholder' => __('Ex: Shop now'),
                    ],
                ]);
        }

        return $form;
    }, 124, 3);

    add_action(BASE_ACTION_AFTER_CREATE_CONTENT, 'save_addition_slider_fields', 120, 3);
    add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, 'save_addition_slider_fields', 120, 3);

    /**
     * @param string $screen
     * @param Request $request
     * @param \Botble\Base\Models\BaseModel $data
     */
    function save_addition_slider_fields($screen, $request, $data)
    {
        if (get_class($data) == SimpleSliderItem::class && $request->has('button_text')) {
            MetaBox::saveMetaBoxData($data, 'button_text', $request->input('button_text'));
        }
    }
}

add_filter(BASE_FILTER_BEFORE_RENDER_FORM, function ($form, $data) {
    switch (get_class($data)) {
        case FlashSale::class:
            $image = MetaBox::getMetaData($data, 'image', true);

            $form
                ->addAfter('end_date', 'image', 'mediaImage', [
                    'label'      => __('Image'),
                    'label_attr' => ['class' => 'control-label'],
                    'value'      => $image,
                ]);
            break;
    }

    return $form;
}, 125, 3);

add_action(BASE_ACTION_AFTER_CREATE_CONTENT, 'save_addition_flash_sales_fields', 125, 3);
add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, 'save_addition_flash_sales_fields', 125, 3);

/**
 * @param string $screen
 * @param Request $request
 * @param \Botble\Base\Models\BaseModel $data
 */
function save_addition_flash_sales_fields($screen, $request, $data)
{
    if (get_class($data) == FlashSale::class && $request->has('image')) {
        MetaBox::saveMetaBoxData($data, 'image', $request->input('image'));
    }
}

if (is_plugin_active('ecommerce') && is_plugin_active('language-advanced')) {
    app()->booted(function () {
        LanguageAdvancedManager::registerModule(FlashSale::class, ['name', 'image']);
    });
}
