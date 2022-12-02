<?php

use Botble\Setting\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $theme = Theme::getThemeName();

        $socialLinks = [];

        if (theme_option('facebook')) {
            $socialLinks[] = [
                [
                    'key'   => 'social-name',
                    'value' => 'Facebook',
                ],
                [
                    'key'   => 'social-icon',
                    'value' => 'fa fa-facebook',
                ],
                [
                    'key'   => 'social-url',
                    'value' => theme_option('facebook'),
                ],
            ];
        }

        if (theme_option('twitter')) {
            $socialLinks[] = [
                [
                    'key'   => 'social-name',
                    'value' => 'Twitter',
                ],
                [
                    'key'   => 'social-icon',
                    'value' => 'fa fa-twitter',
                ],
                [
                    'key'   => 'social-url',
                    'value' => theme_option('twitter'),
                ],
            ];
        }

        if (theme_option('instagram')) {
            $socialLinks[] = [
                [
                    'key'   => 'social-name',
                    'value' => 'Instagram',
                ],
                [
                    'key'   => 'social-icon',
                    'value' => 'fa fa-instagram',
                ],
                [
                    'key'   => 'social-url',
                    'value' => theme_option('instagram'),
                ],
            ];
        }

        if (theme_option('pinterest')) {
            $socialLinks[] = [
                [
                    'key'   => 'social-name',
                    'value' => 'Pinterest',
                ],
                [
                    'key'   => 'social-icon',
                    'value' => 'fa fa-pinterest',
                ],
                [
                    'key'   => 'social-url',
                    'value' => theme_option('pinterest'),
                ],
            ];
        }

        if (theme_option('linkedin')) {
            $socialLinks[] = [
                [
                    'key'   => 'social-name',
                    'value' => 'Linkedin',
                ],
                [
                    'key'   => 'social-icon',
                    'value' => 'fa fa-linkedin',
                ],
                [
                    'key'   => 'social-url',
                    'value' => theme_option('linkedin'),
                ],
            ];
        }

        if (theme_option('youtube')) {
            $socialLinks[] = [
                [
                    'key'   => 'social-name',
                    'value' => 'Youtube',
                ],
                [
                    'key'   => 'social-icon',
                    'value' => 'fa fa-youtube',
                ],
                [
                    'key'   => 'social-url',
                    'value' => theme_option('youtube'),
                ],
            ];
        }

        if (count($socialLinks)) {
            Setting::insertOrIgnore([
                'key'   => 'theme-' . $theme . '-social_links',
                'value' => json_encode($socialLinks),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
