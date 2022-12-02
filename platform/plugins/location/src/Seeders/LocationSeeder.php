<?php

namespace Botble\Location\Seeders;

use BaseHelper;
use Botble\Base\Models\MetaBox;
use Botble\Language\Models\LanguageMeta;
use Botble\Location\Models\City;
use Botble\Location\Models\CityTranslation;
use Botble\Location\Models\Country;
use Botble\Location\Models\CountryTranslation;
use Botble\Location\Models\State;
use Botble\Location\Models\StateTranslation;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            LanguageMeta::whereIn('reference_type', [City::class, State::class, Country::class])->delete();
        }

        City::truncate();
        State::truncate();
        Country::truncate();
        CityTranslation::truncate();
        StateTranslation::truncate();
        CountryTranslation::truncate();
        MetaBox::whereIn('reference_type', [City::class, State::class, Country::class])->delete();

        $dataPath = __DIR__ . '/../../database/files';

        foreach (BaseHelper::scanFolder($dataPath) as $countryCode) {

            $country = file_get_contents($dataPath . '/' . $countryCode . '/country.json');
            $country = json_decode($country, true);

            $country = Country::create($country);

            $states = file_get_contents($dataPath . '/' . $countryCode . '/states.json');
            $states = json_decode($states, true);
            foreach ($states as $state) {
                $state['country_id'] = $country->id;

                State::create($state);
            }

            $cities = file_get_contents($dataPath . '/' . $countryCode . '/cities.json');
            $cities = json_decode($cities, true);
            foreach ($cities as $item) {

                $state = State::where('name', $item['name'])->first();
                if (!$state) {
                    continue;
                }

                foreach ($item['cities'] as $cityName) {
                    $city = [
                        'name'       => $cityName,
                        'state_id'   => $state->id,
                        'country_id' => $country->id,
                    ];

                    City::create($city);
                }
            }
        }
    }
}
