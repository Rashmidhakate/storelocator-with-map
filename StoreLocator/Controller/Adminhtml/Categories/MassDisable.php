<?php
namespace Brainvire\StoreLocator\Controller\Adminhtml\Categories;

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
        $collection = $this->filter->getCollection($this->categoryCollectionFactory->create());
        $collectionSize = $collection->getSize();

        /**
         * @var \Brainvire\StoreLocator\Api\Data\CategoryInterface $category
         */
        foreach ($collection as $category) {
            $category->setIsActive(false);
            $this->categoryRepository->save($category);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 category/ies have been disabled.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
