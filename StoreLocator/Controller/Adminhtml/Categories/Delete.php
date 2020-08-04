<?php
namespace Brainvire\StoreLocator\Controller\Adminhtml\Categories;

use \Brainvire\StoreLocator\Controller\Adminhtml\Categories;

class Delete extends Categories
{
    /**
     * Delete category
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('category_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $this->categoryRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The category has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['category_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a category to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
