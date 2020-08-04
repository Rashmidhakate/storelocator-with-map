<?php
namespace Brainvire\StoreLocator\Controller\Adminhtml\Stores;

use \Brainvire\StoreLocator\Controller\Adminhtml\MassAction;
use \Magento\Backend\App\Action;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\ResponseInterface;

class MassDelete extends MassAction
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

        foreach ($collection as $item) {
            $this->storeRepository->delete($item);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 store(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
