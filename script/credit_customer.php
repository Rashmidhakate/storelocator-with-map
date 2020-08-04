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

$fileName = 'customer_credit_all.csv';
$header = NULL;
$data = array();
if (($handle = fopen($fileName, 'r')) !== FALSE) {
	while (($row = @fgetcsv($handle, 1000, ",")) !== FALSE) {
		if (!$header) {
			$header = $row;
			
		} else {
			$data[] = array_combine($header, $row);
			//print_r($data); die;
		}

	}
}

foreach ($data as $main => $value) {
	try 
	{
		$customer = $_customer->create();
		$customer->setWebsiteId(3);
		$customer->loadByEmail($value['Email']);
	
		$date = explode(' ', $value['CreatedOn']);
		$time = $date[1];
		$date = $date[0];
		$date = explode('/', $date);
		$CreatedOn = '20'.$date[2].'/'.$date[1].'/'.$date[0] . ' ' . $time;

		$creditCustomerCheck = "SELECT store_credit_id FROM `amasty_store_credit` WHERE `customer_id` = ".$customer->getId()."";

		$availableRow = $connection->fetchOne($creditCustomerCheck);
		if($availableRow){
			$creditCustomer = "UPDATE `amasty_store_credit` SET store_credit = ".$value['Balance']." WHERE store_credit_id = ".$availableRow."";
			$connection->query($creditCustomer);
		}
		else{
			$creditCustomer = "INSERT INTO `amasty_store_credit` (customer_id,store_credit) VALUES (".$customer->getId().", ".$value['Balance'].")";
			$connection->query($creditCustomer);
			$creditCustomerCheck = "SELECT store_credit_id FROM `amasty_store_credit` WHERE `customer_id` = ".$customer->getId()."";
			$availableRow = $connection->fetchOne($creditCustomerCheck);
		}
		$is_deduct = 0;
		$action = 1;
		if($value['AmountType'] == 'Payment'){
			$is_deduct = 1;
			$action = 4;
		}
		$actionData = '["'.$value['OrderNumber'].'"]';
		$creditCustomerHisCnt = "SELECT count(*) FROM `amasty_store_credit_history` WHERE `customer_id` = ".$customer->getId()."";
		
		$count = $connection->fetchOne($creditCustomerHisCnt);
		$count = $count++;

		$creditCustomerHistory = "INSERT INTO amasty_store_credit_history (customer_history_id,customer_id,is_deduct,difference,store_credit_balance,action,action_data,created_at,store_id) VALUES (";

		$creditCustomerHistory .= $count.",";
		$creditCustomerHistory .= $customer->getId().",";
		$creditCustomerHistory .= $is_deduct.",".$value['Amount'].",";
		$creditCustomerHistory .= $value['Balance'].",";
		$creditCustomerHistory .= $action.",'";
		$creditCustomerHistory .= $actionData."','";
		$creditCustomerHistory .= $CreatedOn."',3)";

		$connection->query($creditCustomerHistory);
		
		echo $customer->getId(). " Updated \n";
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}