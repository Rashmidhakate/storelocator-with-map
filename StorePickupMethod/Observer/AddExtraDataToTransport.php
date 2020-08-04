<?php

namespace Brainvire\StorePickupMethod\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class AddExtraDataToTransport implements ObserverInterface
{  

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->_resource = $resource;
        $this->quoteFactory = $quoteFactory;
     }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();
        $order = $transport->getOrder();
        $storeData = $this->getQuote($order);
        foreach($storeData as $store){
            $storeName = $store['name'];
            $storeEmailId = $store['email'];
            $storeContactName = $store['contact_name'];
            $storePhone = $store['phone'];
        }
        $transport['storeName'] = $storeName;
        $transport['store_email'] = $storeEmailId;
        $transport['store_contact_name'] = $storeContactName;
        $transport['store_phone'] = $storePhone;
    }

    public function getQuote(Order $order){
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create()->load($quoteId);
        $storeAddress = $quote->getStoreAddress();
        $storeData = $this->getStates($storeAddress);
        return $storeData;
    }

    public function getStates($storeAddress)
    {
        $adapter = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $select = $adapter->select()
                    ->from('inventory_source')
                    ->where('name=?', $storeAddress);
        return $adapter->fetchAll($select);
    }
}