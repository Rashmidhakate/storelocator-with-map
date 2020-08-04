<?php
namespace Brainvire\StoreLocator\Controller\Adminhtml;

use \Magento\Backend\App\Action;
use \Magento\Framework\View\Result\PageFactory;
use \Brainvire\StoreLocator\Api\StoreRepositoryInterface;
use \Brainvire\StoreLocator\Api\Data\StoreInterfaceFactory;
use \Brainvire\StoreLocator\Helper\Config as ConfigHelper;

abstract class Stores extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Brainvire\StoreLocator\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var \Brainvire\StoreLocator\Api\Data\StoreInterfaceFactory
     */
    protected $storeFactory;

    /**
     * @var \Brainvire\StoreLocator\Helper\Config
     */
    private $configHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Brainvire\StoreLocator\Api\StoreRepositoryInterface $storeRepository
     * @param StoreInterfaceFactory $storeFactory
     * @param \Brainvire\StoreLocator\Helper\Config $configHelper
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        StoreRepositoryInterface $storeRepository,
        StoreInterfaceFactory $storeFactory,
        ConfigHelper $configHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->storeRepository = $storeRepository;
        $this->storeFactory = $storeFactory;
        $this->configHelper = $configHelper;
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
        $resultPage->setActiveMenu('Brainvire_StoreLocator::stores');
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Brainvire_StoreLocator::stores');
    }

    /**
     * @return $this|bool
     */
    protected function checkGoogleApiKey()
    {
        if ($this->configHelper->getGoogleApiKeyBackend() === null) {
            $this->messageManager->addErrorMessage(__('Google Api Key is not set! Go to Stores -> Configuration ->  Extensions -> Store Locator to change extension settings.'));
            /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/index');
        }
        return false;
    }
}
