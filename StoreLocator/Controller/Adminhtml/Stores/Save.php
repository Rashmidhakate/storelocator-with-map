<?php
namespace Brainvire\StoreLocator\Controller\Adminhtml\Stores;

use \Brainvire\StoreLocator\Controller\Adminhtml\Stores;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Brainvire\StoreLocator\Api\StoreRepositoryInterface;
use \Brainvire\StoreLocator\Helper\Config as ConfigHelper;
use \Magento\PageCache\Model\Config;
use \Magento\Framework\App\Cache\TypeListInterface;
use \Brainvire\StoreLocator\Api\Data\StoreInterfaceFactory;

class Save extends Stores
{
    /**
     * @var \Magento\PageCache\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    private $typeList;
    protected $jsonHelper;

    /**
     * Save constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param StoreRepositoryInterface $storeRepository
     * @param StoreInterfaceFactory $storeFactory
     * @param ConfigHelper $configHelper
     * @param Config $config
     * @param TypeListInterface $typeList
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        StoreRepositoryInterface $storeRepository,
        StoreInterfaceFactory $storeFactory,
        ConfigHelper $configHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Config $config,
        TypeListInterface $typeList
    ) {
        $this->config = $config;
        $this->typeList = $typeList;
         $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $resultPageFactory, $storeRepository, $storeFactory, $configHelper);
    }

    /**
     * Save store
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        // $data['working_from'] = implode(':',$this->getRequest()->getParam('working_from'));
        //$data['working_to'] = implode(':', $this->getRequest()->getParam('working_to'));

        //$this->jsonHelper->jsonEncode($dataToEncode);
        $data['store_ids'] = implode(',', $this->getRequest()->getParam('store_ids'));
        if ($this->config->isEnabled()) {
            $this->typeList->invalidate('full_page');
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $store = $this->storeFactory->create();
            $store->setData($data);

            $this->_eventManager->dispatch(
                'storelocator_store_prepare_save',
                ['store' => $store, 'request' => $this->getRequest()]
            );

            try {
                
                $this->storeRepository->save($store);

               // $this->messageManager->addSuccessMessage(__('The store has been saved.'));
                 $this->messageManager->addSuccessMessage(__('The store has been saved.'));
                $this->_getSession()->setFormData(false);

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['store_id' => $store->getId(), '_current' => true, 'active_tab' => 'store_info']);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the store.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['store_id' => $this->getRequest()->getParam('store_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }
}
