<?php
namespace Brainvire\StoreLocator\Model\StoreLocator\System\Config;

use \Magento\Framework\Option\ArrayInterface;
use \Brainvire\StoreLocator\Model\ResourceModel\Category\CollectionFactory;
use \Brainvire\StoreLocator\Model\Category;

class Categories implements ArrayInterface
{
    /**
     * @var \Brainvire\StoreLocator\Model\Store
     */
    private $categoriesCollectionFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->categoriesCollectionFactory = $collectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [
            '' => __('--Please Select--')
        ];
        $collection = $this->categoriesCollectionFactory->create();
        $collection->addFieldToFilter('is_active', Category::STATUS_ACTIVE);
        $collection->addFieldToSelect(['category_id', 'name']);
        $collection->load();

        foreach ($collection as $category) {
            $options[$category->getId()] = $category->getName();
        }

        return $options;
    }
}
