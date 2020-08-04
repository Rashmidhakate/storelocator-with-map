<?php

namespace Brainvire\StoreLocator\Controller\Adminhtml\Stores;

use \Brainvire\StoreLocator\Controller\Adminhtml\MassAction;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\ResponseInterface;

class MassDisable extends MassAction
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->storeCollectionFactory->create());
        $collectionSize = $collection->getSize();

        /**
         * @var \Brainvire\StoreLocator\Api\Data\StoreInterface $store
         */
        foreach ($collection as $store) {
            $store->setIsActive(false);
            $this->storeRepository->save($store);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 store(s) have been disabled.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
