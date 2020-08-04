<?php
namespace Brainvire\StoreLocator\Model\StoreLocator\Source;

use \Magento\Framework\Data\OptionSourceInterface;
use \Brainvire\StoreLocator\Model\ResourceModel\Category\CollectionFactory;
use \Brainvire\StoreLocator\Model\Category;

class Categories implements OptionSourceInterface
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
        $options[] = ['label' => '', 'value' => ''];
        $collection = $this->categoriesCollectionFactory->create();
        $collection->addFieldToFilter('is_active', Category::STATUS_ACTIVE);
        $collection->addFieldToSelect(['category_id', 'name']);
        $collection->load();

        foreach ($collection as $category) {
            $options[] = ['label' => $category->getName(), 'value' => $category->getId()];
        }
        return $options;
    }
}
