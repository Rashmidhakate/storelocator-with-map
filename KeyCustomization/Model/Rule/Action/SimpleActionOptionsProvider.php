<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Brainvire\KeyCustomization\Model\Rule\Action;

class SimpleActionOptionsProvider extends \Magento\CatalogRule\Model\Rule\Action\SimpleActionOptionsProvider
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Apply as percentage of original'),
                'value' => 'by_percent'
            ],
            [
                'label' => __('Apply as fixed amount'),
                'value' => 'by_fixed'
            ],
            [
                'label' => __('Adjust final price to this percentage'),
                'value' => 'to_percent'
            ],
            [
                'label' => __('Adjust final price to discount value'),
                'value' => 'to_fixed'
            ],
            [
                'label' => __('2 Key Discount'),
                'value' => 'two_key'
            ],
            [
                'label' => __('3 Key Discount'),
                'value' => 'three_key'
            ],
            [
                'label' => __('4 Key Discount'),
                'value' => 'four_key'
            ]
        ];
    }
}
