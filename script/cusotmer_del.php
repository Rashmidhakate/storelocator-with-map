<?php

use Magento\Framework\App\Bootstrap;

require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$obj->get('Magento\Framework\Registry')->register('isSecureArea', true);
//$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');

$countryFactory = $obj->get('\Magento\Directory\Model\CountryFactory');

$country = $countryFactory->create();
$countryCollection = $country->getCollection();

/* end country collection */

/* database connection */

$resource = $obj->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();

/* end database connection */

//$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');

//$storeManager = $obj->get('Magento\Store\Model\StoreManagerInterface');
$product = $obj->get('Magento\Catalog\Model\Product');
$quoteFactory = $obj->get('Magento\Quote\Model\QuoteFactory');
$quoteManagement = $obj->get('Magento\Quote\Model\QuoteManagement');
$custobjerFactory = $obj->get('Magento\Customer\Model\CustomerFactory');
$custobjerRepository = $obj->get('Magento\Customer\Api\CustomerRepositoryInterface');
$orderService = $obj->get('Magento\Sales\Model\Service\OrderService');
$cart = $obj->get('Magento\Checkout\Model\Cart');
$productFactory = $obj->get('Magento\Catalog\Model\ProductFactory');
$cartRepositoryInterface = $obj->get('Magento\Quote\Api\CartRepositoryInterface');
$cartManagementInterface = $obj->get('Magento\Quote\Api\CartManagementInterface');
$productRepository = $obj->get('\Magento\Catalog\Api\ProductRepositoryInterface');
$invoiceService = $obj->get('\Magento\Sales\Model\Service\InvoiceService');
$transaction = $obj->get('\Magento\Framework\DB\Transaction');
$invoiceSender = $obj->get('\Magento\Sales\Model\Order\Email\Sender\InvoiceSender');
$creditmemoFactory = $obj->get('\Magento\Sales\Model\Order\CreditmemoFactory');
$invoice = $obj->get('\Magento\Sales\Model\Order\Invoice');
$creditmemoService = $obj->get('\Magento\Sales\Model\Service\CreditmemoService');
$scopeConfig = $obj->create('\Magento\Framework\App\Config\ScopeConfigInterface');
$configResourceModel = $obj->create('\Magento\Config\Model\ResourceModel\Config');
$cacheTypeList = $obj->create('\Magento\Framework\App\Cache\TypeListInterface');
$cacheFrontendPool = $obj->create('\Magento\Framework\App\Cache\Frontend\Pool');
$shipmentCollection = $obj->create('Magento\Sales\Model\Order\Shipment');

$notifier = $obj->create('Magento\Sales\Model\OrderNotifier');
$invoiceCollection = $obj->create('Magento\Sales\Model\Order\Invoice');
$creditmemoCollection = $obj->create('Magento\Sales\Model\Order\Creditmemo');
$orderHistory = $obj->create('Magento\Sales\Model\ResourceModel\Order\Status\History\Collection');
$orderFactory = $obj->get('Magento\Sales\Model\OrderFactory');
$orderItemFactory = $obj->get('Magento\Sales\Model\Order\ItemFactory');
$orderPaymentRepository = $obj->get('Magento\Sales\Api\OrderPaymentRepositoryInterface');
$orderAddressRepository = $obj->get('Magento\Sales\Model\Order\AddressRepository');
$_customer = $obj->get('\Magento\Customer\Model\CustomerFactory');

$fileName = 'customer_del.csv';
if (($handle = fopen($fileName, 'r')) !== FALSE) {
	while (!feof($handle)) {
		$line_of_text[] = fgetcsv($handle);
	}
}

foreach ($line_of_text as $main => $values) {

	try {

		$customer = $_customer->create();
		$customer->setWebsiteId(3);
		$cust = $customer->loadByEmail($values[0]);
		if(!empty($cust->getData())) {
			//print_r($cust->getData());
			
			//die;	
			echo $cust->getId(). " Updated \n";
			//$cust->delete();
			//die;
		}
		

		// $orderGrid = "update `sales_order_grid` set customer_id = '".$customer->getId()."' WHERE `entity_id` = " . $order->getId(). " AND `store_id` = 3";
		// $connection->query($orderGrid);
		
		
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}