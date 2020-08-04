<?php
namespace Brainvire\StoreLocator\Model\Config\Source;

use \Magento\Framework\Option\ArrayInterface;

class GroupBy implements ArrayInterface
{
    const DONT_GROUP = 0;
    const COUNTRY = 1;
    const CITY = 2;
    const COUNTRY_CITY = 3;
    const CITY_COUNTRY = 4;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::DONT_GROUP, 'label' => 'Don\'t group'],
            ['value' => self::COUNTRY, 'label' => 'Country'],
            ['value' => self::CATEGORY, 'label' => 'City'],
            ['value' => self::COUNTRY_CITY, 'label' => 'Country -> City'],
            ['value' => self::CITY_COUNTRY, 'label' => 'City -> Country'],
        ];
    }
}
