<?php

namespace Brainvire\StorePickupMethod\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\TransportBuilderByStore;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\DataObject;

class SendCopyMail implements \Magento\Framework\Event\ObserverInterface
{
    const XML_PATH_EMAIL_TEMPLATE = 'storepickup/general/template';
    //const XML_PATH_EMAIL_TEMPLATE = 'sales_email/order/template';

	public function __construct(
        \Magento\GoogleAdwords\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order $collection,
        Template $templateContainer,
        OrderIdentity $identityContainer,
        TransportBuilder $transportBuilder,
        PaymentHelper $paymentHelper,
        Renderer $addressRenderer,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        TransportBuilderByStore $transportBuilderByStore = null
    ) {
        $this->_helper = $helper;
        $this->_collection = $collection;
        $this->_registry = $registry;
        $this->templateContainer = $templateContainer;
        $this->identityContainer = $identityContainer;
        $this->transportBuilder = $transportBuilder;
        $this->paymentHelper = $paymentHelper;
        $this->addressRenderer = $addressRenderer;
		$this->scopeConfig = $scopeConfig;
		$this->quoteFactory = $quoteFactory;
		$this->_resource = $resource;
        $this->transportBuilderByStore = $transportBuilderByStore ?: ObjectManager::getInstance()->get(
            TransportBuilderByStore::class
        );
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$orderIds = $observer->getOrderIds();
		if (!$orderIds || !is_array($orderIds)) {
		return $this;
		}
		foreach($orderIds as $id){
			$order = $this->_collection->load($id);
			$quoteId = $order->getQuoteId();
			$quote = $this->quoteFactory->create()->load($quoteId);
			$storeAddress = $quote->getStoreAddress();
			$storeData = $this->getStates($storeAddress);
		
			foreach($storeData as $store){
				$storeName = $store['name'];
				$storeEmailId = $store['email'];
				$storeContactName = $store['contact_name'];
				$storePhone = $store['phone'];
			}
			$this->temp_id = $this->getTemplateId(self::XML_PATH_EMAIL_TEMPLATE);
			//$order = $this->_collection->addFieldToFilter('entity_id', ['in' => $orderIds]);
			$this->transportBuilder->setTemplateIdentifier($this->temp_id);
			$this->transportBuilder->setTemplateOptions($this->getTemplateOptions());
			$transport = [
				'order' => $order,
				'billing' => $order->getBillingAddress(),
				'payment_html' => $this->getPaymentHtml($order),
				'store' => $order->getStore(),
				'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
				'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
				'storeName' => $storeName,
				'store_email' => $storeEmailId,
				'store_contact_name' => $storeContactName,
				'store_phone' => $storePhone
			];
			$transportObject = new DataObject($transport);
			$this->transportBuilder->setTemplateVars($transportObject->getData());
			$this->transportBuilderByStore->setFromByStore(
				$this->identityContainer->getEmailIdentity(),
				$this->identityContainer->getStore()->getId()
			);
			$this->transportBuilder->addTo($storeEmailId);
			$transport = $this->transportBuilder->getTransport();
			$transport->sendMessage();
		}
		return $this;
	}

	public function getStates($storeAddress)
    {
        $adapter = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $select = $adapter->select()
                    ->from('inventory_source')
                    ->where('name=?', $storeAddress);
        return $adapter->fetchAll($select);
    }

	 /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

     /**
     * Return template id according to store
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->identityContainer->getStore()->getStoreId());
    }
	/**
	* @return array
	*/
	protected function getTemplateOptions()
	{
		return [
			'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
			'store' => $this->identityContainer->getStore()->getStoreId()
		];
	}
	
    /**
     * Get payment info block as html
     *
     * @param Order $order
     * @return string
     */
    protected function getPaymentHtml(Order $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $this->identityContainer->getStore()->getStoreId()
        );
    }

     /**
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedShippingAddress(Order $order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedBillingAddress(Order $order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

}