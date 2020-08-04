<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Brainvire\StockSourceImport\Model\Source;

use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory;
/**
 * Source import entity model
 *
 * @api
 * @since 100.0.2
 */
class Entity implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $sourceCollectionFactory
    ) {
        $this->_sourceCollectionFactory = $sourceCollectionFactory;
    }

     
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $collection = $this->_sourceCollectionFactory->create();
        $sourceArray = array();
        $sourceArray[] = ['value' => '','label' => __('-- Please Select --')];
        foreach ($collection as $source) {
            $sourceId = $source->getsourceCode();
            $name = $source->getName();
            $sourceArray[] = ['value' => $sourceId, 'label' => $name ]; 
        }
        return $sourceArray;
    }
}
