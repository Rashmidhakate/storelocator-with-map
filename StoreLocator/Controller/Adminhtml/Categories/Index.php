<?php
namespace Brainvire\StoreLocator\Controller\Adminhtml\Categories;

use \Brainvire\StoreLocator\Controller\Adminhtml\Categories;

class Index extends Categories
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Store Locator - Categories'));

        return $resultPage;
    }
}
