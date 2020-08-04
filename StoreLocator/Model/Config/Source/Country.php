<?php
namespace Brainvire\StoreLocator\Model\Config\Source;

use \Magento\Framework\Option\ArrayInterface;
use \Magento\Directory\Model\ResourceModel\Country\Collection as CountryCollection;

class Country implements ArrayInterface
{
    /**
     * Countries
     *
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    private $countryCollection;

    /**
     * @param \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
     */
    public function __construct(CountryCollection $countryCollection)
    {
        $this->countryCollection = $countryCollection;
    }

    /**
     * Options array
     *
     * @var array
     */
    private $options;

    /**
     * Return options array
     *
     * @param boolean $isMultiselect
     * @param string|array $foregroundCountries
     * @return array
     */
    public function toOptionArray($isMultiselect = false, $foregroundCountries = '')
    {
        $optionsArr = [];
        if (!$this->options) {
            $this->options = $this->countryCollection->loadData()->setForegroundCountries(
                $foregroundCountries
            )->toOptionArray(
                false
            );
        }

        $options = $this->options;
        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => __('--Please Select--')]);
        }

        foreach ($options as $option) {
            $optionsArr[$option['value']] = $option['label'];
        }

        return $optionsArr;
    }
}
