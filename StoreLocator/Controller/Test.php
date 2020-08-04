<?php
namespace Brainvire\StoreLocator\Controller\Test;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Brainvire\StoreLocator\Model\StoreFactory;

class Test extends Action
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    // public function execute()
    // {
    //     $resultPage = $this->resultPageFactory->create();
    //     $resultPage->getConfig()->getTitle()->set(__('Store Locator'));

    //     return $resultPage;
    // }


    protected $_modelStoreFactory;

    public function __construct(
        Context $context, 
        StoreFactory $modelStoreFactory
    ) {
        parent::__construct($context);
        $this->_modelStoreFactory = $modelStoreFactory;
    }

    public function execute()
    {

        $resultPage = $this->_modelStoreFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Store Locator'));
        $collection = $resultPage->getCollection(); 
        var_dump($collection->getData());
        exit;

    }
}
