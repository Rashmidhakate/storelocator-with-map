<?php
namespace Brainvire\StoreLocator\Model;

use \Magento\Framework\Model\AbstractModel;
use \Brainvire\StoreLocator\Api\Data\CategoryInterface;
use \Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Model\ResourceModel\AbstractResource;
use \Magento\Framework\Data\Collection\AbstractDb;
use \Brainvire\StoreLocator\Model\ResourceModel\Category as ResourceModel;

class Category extends AbstractModel implements CategoryInterface, IdentityInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const CACHE_TAG = 'brainvire_storelocator_category';

    /**
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_cacheTag = 'brainvire_storelocator_category';
        $this->_eventPrefix = 'brainvire_storelocator_category';
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Prepare store's types.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ACTIVE => __('Enabled'), self::STATUS_INACTIVE => __('Disabled')];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreationTime($creationTime)
    {
        $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdateTime($updateTime)
    {
        $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
    {
        $this->setData(self::IS_ACTIVE, $isActive);
    }
}
