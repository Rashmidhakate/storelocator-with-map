<?php
namespace Brainvire\StoreLocator\Controller\Adminhtml\Stores;

use \Brainvire\StoreLocator\Controller\Adminhtml\Stores;

class Index extends Stores
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Store Locator - Stores'));

        return $resultPage;
    }
}
