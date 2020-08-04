<?php
namespace Brainvire\StoreLocator\Controller\Adminhtml;

use \Magento\Backend\App\Action;
use \Magento\Ui\Component\MassAction\Filter;
use \Brainvire\StoreLocator\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use \Brainvire\StoreLocator\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use \Brainvire\StoreLocator\Api\StoreRepositoryInterface;
use \Brainvire\StoreLocator\Api\CategoryRepositoryInterface;

abstract class MassAction extends Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Brainvire\StoreLocator\Model\ResourceModel\Store\CollectionFactory
     */
    protected $storeCollectionFactory;

    /**
     * @var \Brainvire\StoreLocator\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Brainvire\StoreLocator\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var \Brainvire\StoreLocator\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param StoreCollectionFactory $storeCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param \Brainvire\StoreLocator\Api\StoreRepositoryInterface $storeRepository
     * @param \Brainvire\StoreLocator\Api\CategoryRepositoryInterface $categoryRepository
     * @internal param StoreCollectionFactory $collectionFactory
     * @internal param CategoryCollectionFactoryCategoryCollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        StoreCollectionFactory $storeCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreRepositoryInterface $storeRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->filter = $filter;
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeRepository = $storeRepository;
        $this->categoryRepository= $categoryRepository;
        parent::__construct($context);
    }
}
