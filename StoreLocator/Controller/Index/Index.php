<?php
namespace Brainvire\StoreLocator\Controller\Index;

class Index extends \Brainvire\StoreLocator\Controller\Index
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Store Locator'));

        return $resultPage;
    }
}
