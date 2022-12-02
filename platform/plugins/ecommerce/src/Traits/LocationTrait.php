<?php

namespace Botble\Ecommerce\Traits;

use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Location\Repositories\Interfaces\StateInterface;
use EcommerceHelper;

/**
 * @mixin \Eloquent
 */
trait LocationTrait
{
    /**
     * @return string
     */
    public function getCountryNameAttribute()
    {
        return EcommerceHelper::getCountryNameById($this->country);
    }

    /**
     * @return string
     */
    public function getStateNameAttribute()
    {
        $value = $this->state;

        if (!$value || !is_plugin_active('location')) {
            return $value;
        }

        $stateName = app(StateInterface::class)->getModel()->where('id', $value)->value('name');

        if ($stateName) {
            return $stateName;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getCityNameAttribute()
    {
        $value = $this->city;

        if (!$value || !is_plugin_active('location')) {
            return $value;
        }

        $cityName = app(CityInterface::class)->getModel()->where('id', $value)->value('name');

        if ($cityName) {
            return $cityName;
        }

        return $value;
    }
}
