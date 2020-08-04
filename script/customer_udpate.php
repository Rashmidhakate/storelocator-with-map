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

$fileName = 'customer_update.csv';
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

	try {
		$Email = $value['Email'];
        $AffiliateID = $value['AffiliateID'];
        $CreatedOn = $value['CreatedOn'];
        $CIM_ProfileId = $value['CIM_ProfileId']; 
        $CompanyWebsite = $value['CompanyWebsite'];
        $TypeOfStore = $value['TypeOfStore'];
        $TotalStores = $value['TotalStores'];

		$customer = $_customer->create();
		$customer->setWebsiteId(3);
		$customer->loadByEmail($Email);
		if(!empty($customer->getData())){
			$customer->setAffiliateId($AffiliateID);
			$customer->setCimProfileId($CIM_ProfileId);
			// 2019-02-19 13:35:00
			// 17/09/13 20:46
			// $CreatedOn = '2017/09/13 20:46';
			$date = explode(' ', $CreatedOn);
			$time = $date[1];
			$date = $date[0];
			$date = explode('/', $date);
			$CreatedOn = '20'.$date[2].'/'.$date[1].'/'.$date[0] . ' ' . $time;

			// echo $CreatedOn . "\n";
			$shippon_date_timestamp = strtotime($CreatedOn);
			// echo $shippon_date_timestamp . "\n";
			$CreatedOn = date('Y-m-d H:i:s', $shippon_date_timestamp);
			// echo $CreatedOn . "\n"; die;
			$customer->setCreatedAt($CreatedOn);
			$customer->setCompanyWebsite($CompanyWebsite);
			if($TypeOfStore != '')
			{
				if($TypeOfStore == 'Online'){
					$TypeOfStore = 443;
				}
				else if($TypeOfStore == 'Kiosk'){
					$TypeOfStore = 444;
				}
				else if($TypeOfStore == 'Brick and Mortar'){
					$TypeOfStore = 442;
				}
				else if($TypeOfStore == 'Other'){
					$TypeOfStore = 445;
				}
				$customer->setTypeOfStore($TypeOfStore);
			}
			$customer->setHowManyStores($TotalStores);
			$customer->save();		
			echo $customer->getId(). " Updated \n";
		}
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}