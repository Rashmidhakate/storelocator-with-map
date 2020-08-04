<?php
namespace Brainvire\StoreLocator\Controller\Adminhtml;

use \Magento\Backend\App\Action;
use \Magento\Framework\View\Result\PageFactory;
use \Brainvire\StoreLocator\Api\CategoryRepositoryInterface;
use \Brainvire\StoreLocator\Api\Data\CategoryInterfaceFactory;

abstract class Categories extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Brainvire\StoreLocator\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Brainvire\StoreLocator\Api\Data\CategoryInterfaceFactory
     */
    protected $categoryFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Brainvire\StoreLocator\Api\CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterfaceFactory $categoryFactory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Brainvire_StoreLocator::categories');
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Brainvire_StoreLocator::categories');
    }
}
