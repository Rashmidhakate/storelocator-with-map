<?php

use Magento\Framework\App\Bootstrap;

require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();
// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$obj->get('Magento\Framework\Registry')->register('isSecureArea', true);
$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');

$countryFactory = $obj->get('\Magento\Directory\Model\CountryFactory');

$country = $countryFactory->create();
$countryCollection = $country->getCollection();

/* end country collection */

/* database connection */

$resource = $obj->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();

/* end database connection */

$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');

$storeManager = $obj->get('Magento\Store\Model\StoreManagerInterface');
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
$fileName = 'order_details.csv';
if (($handle = fopen($fileName, 'r')) !== FALSE) {
	while (!feof($handle)) {
		$line_of_text[] = fgetcsv($handle);
	}
}

foreach ($line_of_text as $main => $values) {
	if ($main == 0) {
		$headers[$main] = $values;
	} else {
		$item = [];
		$shipping = [];
		$billing = [];
		$fedex = [];
		$group = 0;
		if (empty($values)) {
			continue;
		}
		foreach ($values as $key => $value) {

			if ($headers[0][$key] == "OrderNumber") {
				$group = $value;
				$orders[$group][$headers[0][$key]] = htmlentities($value);
			} else {
				if ($headers[0][$key] == 'OrderedProductSKU') {
					if ($value == 'NULL' || $value == '') {
						$value = 'Value Missing';
					}
					$item['sku'] = $value;
				} else if ($headers[0][$key] == 'Name') {
					if ($value == 'NULL' || $value == '') {
						$value = 'Value Missing';
					}
					$item['name'] = $value;
				} else if ($headers[0][$key] == 'Quantity') {
					$item['qty'] = $value;
				} else if ($headers[0][$key] == 'ChosenSize') {
					$item['size'] = $value;
				} else if ($headers[0][$key] == 'OrderedProductVariantName') {
					$item['width'] = $value;
				} else if ($headers[0][$key] == 'OrderedProductPrice') {
					$item['price'] = $value;
				} else if ($headers[0][$key] == 'OrderedProductRegularPrice') {
					$item['regularprice'] = $value;
				} else if ($headers[0][$key] == 'EngravePrice') {
					$item['engraveprice'] = $value;
				} else if ($headers[0][$key] == 'EngravingText') {
					$item['engravetext'] = $value;
				} else if ($headers[0][$key] == 'ProductID') {
					$item['product_id'] = $value;
				} else if ($headers[0][$key] == 'LevelDiscountPercent') {
					$item['discount'] = $value;
				} else {

					$orders[$group][$headers[0][$key]] = htmlentities($value);
				}
			}

		}
		$orders[$group]['items'][] = $item;
	}
}
// echo "<pre>";
// print_r($orders);
// exit;

$orderData = array();
$itemsData = array();
$orderCount = 0;
foreach ($orders as $csvdata) {
	$totalOrderedProductRegularPrice = 0;
	$totalOrderedProductPrice = 0;
	$totalEngravePrice = 0;
	$discountAmount = 0;
	$subTotalWithDiscount = 0;
	$orderCount++;
	echo $orderCount . "\n";
	$OrderNumber = $csvdata["OrderNumber"];
	$FirstName = $csvdata["FirstName"];
	$LastName = $csvdata["LastName"];
	$Email = $csvdata["Email"];
	$BillingFirstName = $csvdata["BillingFirstName"];
	$BillingLastName = $csvdata["BillingLastName"];
	$BillingCompany = $csvdata["BillingCompany"];
	$BillingAddress1 = $csvdata["BillingAddress1"];
	$BillingCity = $csvdata["BillingCity"];
	$BillingState = $csvdata["BillingState"];
	$BillingZip = $csvdata["BillingZip"];
	$BillingCountry = $csvdata["BillingCountry"];
	$BillingPhone = $csvdata["BillingPhone"];
	$ShippingFirstName = $csvdata["ShippingFirstName"];
	$ShippingLastName = $csvdata["ShippingLastName"];
	$ShippingCompany = $csvdata["ShippingCompany"];
	$ShippingAddress1 = $csvdata["ShippingAddress1"];
	$ShippingCity = $csvdata["ShippingCity"];
	$ShippingState = $csvdata["ShippingState"];
	$ShippingZip = $csvdata["ShippingZip"];
	$ShippingCountry = $csvdata["ShippingCountry"];
	$ShippingMethod = $csvdata["ShippingMethod"];
	$ShippingPhone = $csvdata["ShippingPhone"];
	$CardType = $csvdata["CardType"];
	$CardName = $csvdata["CardName"];
	$CardNumber = $csvdata["CardNumber"];
	$CardExpirationMonth = $csvdata["CardExpirationMonth"];
	$CardExpirationYear = $csvdata["CardExpirationYear"];
	$CardStartDate = $csvdata["CardStartDate"];
	$CardIssueNumber = $csvdata["CardIssueNumber"];
	$OrderSubtotal = $csvdata["OrderSubtotal"];
	$OrderTax = $csvdata["OrderTax"];
	$OrderShippingCosts = $csvdata["OrderShippingCosts"];
	$OrderTotal = $csvdata["OrderTotal"];
	$PaymentGateway = $csvdata["PaymentGateway"];
	$AuthorizationCode = $csvdata["AuthorizationCode"];
	$AuthorizationResult = $csvdata["AuthorizationResult"];
	$AuthorizationPNREF = $csvdata["AuthorizationPNREF"];
	$ShippedOn = $csvdata["ShippedOn"];
	$OrderDate = $csvdata["OrderDate"];
	$PaymentMethod = $csvdata["PaymentMethod"];
	$OrderNotes = $csvdata["OrderNotes"];
	$PONumber = $csvdata["PONumber"];
	$ReceiptEmailSentOn = $csvdata["ReceiptEmailSentOn"];
	$ShippingTrackingNumber = $csvdata["ShippingTrackingNumber"];
	$ShippedVIA = $csvdata["ShippedVIA"];
	$TransactionState = $csvdata["TransactionState"];
	//$EngravePrice = $csvdata["EngravePrice"];
	//$LevelDiscountPercent = $csvdata["LevelDiscountPercent"];
	$AuthorizedOn = $csvdata["AuthorizedOn"];
	$CapturedOn = $csvdata["CapturedOn"];
	$RefundedOn = $csvdata["RefundedOn"];

	/* -- date format-- */
	$shippon_date_timestamp = strtotime($ShippedOn);
	$shippon = date('Y-m-d H:i:s', $shippon_date_timestamp);
	//echo $shippon . "\n";
	$orderon_date_timestamp = strtotime($OrderDate);
	$orderon = date('Y-m-d H:i:s', $orderon_date_timestamp);
	//echo $orderon . "\n";
	$AuthorizedOn_date_timestamp = strtotime($AuthorizedOn);
	$authorizeon = date('Y-m-d H:i:s', $AuthorizedOn_date_timestamp);
	//echo $authorizeon . "\n";
	$CapturedOn_date_timestamp = strtotime($CapturedOn);
	$captureon = date('Y-m-d H:i:s', $CapturedOn_date_timestamp);
	//echo $captureon . "\n";
	$RefundedOn_date_timestamp = strtotime($RefundedOn);
	$refundon = date('Y-m-d H:i:s', $RefundedOn_date_timestamp);
	// echo $refundon . "\n";
	// exit;
	//$itemsData["items"] = array();
	$option = array();
	try {

		/* get country code by country name */

		foreach ($countryCollection as $country) {
			if ($ShippingCountry == $country->getName()) {
				$shippingCountryId = $country->getCountryId();
				break;
			}
		}

		/*check for country id , region available in database or not */

		$shippingRegionId = "SELECT region_id FROM `directory_country_region` WHERE `country_id` = '" . trim($shippingCountryId) . "' AND `code` = '" . trim($ShippingState) . "' ";
		$result_shippingRegionId = $connection->fetchOne($shippingRegionId);

		if ($result_shippingRegionId) {
			$shipping_region_id = $result_shippingRegionId;
		} else {
			$shipping_region_id = $ShippingState;
		}

		foreach ($countryCollection as $country) {
			if ($BillingCountry == $country->getName()) {
				$billingCountryId = $country->getCountryId();
				break;
			}
		}

		/*check for country id , region available in database or not */

		$billingRegionId = "SELECT region_id FROM `directory_country_region` WHERE `country_id` = '" . trim($billingCountryId) . "' AND `code` = '" . trim($BillingState) . "' ";
		$result_billingRegionId = $connection->fetchOne($billingRegionId);

		if ($result_billingRegionId) {
			$billing_region_id = $result_billingRegionId;
		} else {
			$billing_region_id = $BillingState;
		}
		//print_r($itemsData["items"]);
		$orderData = [
			'currency_id' => 'USD',
			'email' => $Email,
			'shipping_address' => [
				'firstname' => $ShippingFirstName, //address Details
				'lastname' => $ShippingLastName,
				'street' => $ShippingAddress1,
				'city' => $ShippingCity,
				'country_id' => $shippingCountryId,
				'region_id' => $shipping_region_id,
				'postcode' => $ShippingZip,
				'telephone' => $ShippingPhone,
				'save_in_address_book' => 1,
			],
			'billing_address' => [
				'firstname' => $BillingFirstName, //address Details
				'lastname' => $BillingLastName,
				'street' => $BillingAddress1,
				'city' => $BillingCity,
				'country_id' => $billingCountryId,
				'region_id' => $billing_region_id,
				'postcode' => $BillingZip,
				'telephone' => $BillingPhone,
				'save_in_address_book' => 1,
			],
		];
		// echo "<pre>";
		// print_r($csvdata["items"]);
		// exit;
		$currency = 'USD'; //$orderData['currency_code'];
		$order = $orderFactory->create()->setWebsiteId(2);
		$order->setStoreId(2);
		$order
			->setGlobalCurrencyCode($currency)
			->setBaseCurrencyCode($currency)
			->setStoreCurrencyCode($currency)
			->setOrderCurrencyCode($currency);
		$orderPayment = $orderPaymentRepository->create();
		$method = 'newpayment';
		$additionalInfo = ["method_title" => "$PaymentGateway"];
		$orderPayment->setMethod($method);
		$orderPayment->setAdditionalInformation($additionalInfo);
		$order->setPayment($orderPayment);
		$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');
		$state = $obj->get('\Magento\Framework\App\State');
		$customer = $_customer->create();
		$customer->setWebsiteId(2);
		$customer->loadByEmail($orderData['email']);

		if (!$customer->getEntityId()) {
			$customer->setWebsiteId(2)
				->setStoreId(2)
				->setFirstname($orderData['billing_address']['firstname'])
				->setLastname($orderData['billing_address']['lastname'])
				->setEmail($orderData['email'])
				->setPassword("Demo@123");
			$customer->save();
		}
		// echo "<pre>";
		// print_r($customer->getData());
		// exit;
		$order
			->setCustomerId($customer->getId())
			->setCustomerEmail($customer->getEmail())
			->setCustomerFirstname($customer->getFirstname())
			->setCustomerLastname($customer->getLastname())
			->setCustomerGroupId($customer->getGroupId())
			->setCustomerIsGuest(0);

		/* @var $orderAddressRepository \Magento\Sales\Model\Order\AddressRepository */
		$orderAddress = $orderAddressRepository->create();
		$orderAddress
			->setWebsiteId(2)
			->setStoreId(2)
			->setAddressType(\Magento\Sales\Model\Order\Address::TYPE_BILLING)
			->setCustomerId($customer->getId())
			->setFirstname($orderData['billing_address']['firstname'])
			->setLastname($orderData['billing_address']['lastname'])
			->setStreet($orderData['billing_address']['street'])
			->setCity($orderData['billing_address']['city'])
			->setPostcode($orderData['billing_address']['postcode'])
			->setTelephone($orderData['billing_address']['telephone'])
			->setFax('')
			->setCountryId($orderData['billing_address']['country_id'])
			->setRegion($orderData['billing_address']['region_id']);
		$order->setBillingAddress($orderAddress);
		// repeat for shipping address $order->setShippingAddress($orderAddress);
		$orderAddress = $orderAddressRepository->create();
		$orderAddress
			->setWebsiteId(2)
			->setStoreId(2)
			->setAddressType(\Magento\Sales\Model\Order\Address::TYPE_SHIPPING)
			->setCustomerId($customer->getId())
			->setFirstname($orderData['shipping_address']['firstname'])
			->setLastname($orderData['shipping_address']['lastname'])
			->setStreet($orderData['shipping_address']['street'])
			->setCity($orderData['shipping_address']['city'])
			->setPostcode($orderData['shipping_address']['postcode'])
			->setTelephone($orderData['shipping_address']['telephone'])
			->setFax('')
			->setCountryId($orderData['shipping_address']['country_id'])
			->setRegion($orderData['shipping_address']['region_id']);

		$order->setShippingAddress($orderAddress);

		$order
			->setShippingMethod('freeshipping_freeshipping')
			->setShippingDescription($ShippingMethod);
		$products = $csvdata["items"];

		foreach ($csvdata["items"] as $items) {
			// echo "<pre>";
			// print_r($items);
			$sku = $items["sku"];
			$name = $items["name"];
			$Quantity = $items["qty"];
			$ChosenSize = trim($items["size"]);
			$OrderedProductVariantName = trim($items["width"]);
			$LevelDiscountPercent = $items["discount"];
			$OrderedProductPrice = $items["price"];
			//$totalOrderedProductPrice += $OrderedProductPrice;
			$OrderedProductRegularPrice = $items["regularprice"];
			$totalOrderedProductRegularPrice += $OrderedProductRegularPrice;
			$EngravePrice = $items["engraveprice"];
			$engravetext = $items["engravetext"];
			$ProductID = $items["product_id"];
			$OrderedProductPriceWithEngrave = (float) $OrderedProductPrice + $EngravePrice;
			if ($LevelDiscountPercent) {

				$discountAmount = (float) $OrderedProductRegularPrice - $OrderedProductPriceWithEngrave;
				// $discountAmount = ($OrderedProductRegularPrice * $LevelDiscountPercent) / 100;
				// $discountAmountTotal += $discountAmount;
			}
			$totalOrderedProductPrice += (float) $OrderedProductPriceWithEngrave;
			$requestInfo = [
				'qty' => $Quantity,

			];
			$super_attribute = [];
			if (isset($ChosenSize) && strtolower($ChosenSize) != 'null') {
				$super_attribute[] = ['label' => 'Size', 'value' => $ChosenSize];

			}
			if (isset($EngravePrice) && strtolower($EngravePrice) != 'null') {
				$super_attribute[] = ['label' => 'Engraving', 'value' => $engravetext];

			}
			if (isset($OrderedProductVariantName) && strtolower($OrderedProductVariantName) != 'null') {
				$super_attribute[] = ['label' => 'Width', 'value' => $OrderedProductVariantName];
			}
			$rowTotal = doubleval($items["regularprice"]) * doubleval($items['qty']);

			$orderItem = $orderItemFactory->create();
			$orderItem
				->setWebsiteId(2)
				->setStoreId(2)
				->setQuoteItemId(0)
				->setQuoteParentItemId(NULL)
				->setProductId($items["product_id"])
				->setProductType('simple')
				->setProductOptions(['info_buyRequest' => $requestInfo, 'additional_options' => $super_attribute])
				->setQtyBackordered(NULL)
				->setTotalQtyOrdered($items['qty'])
				->setQtyOrdered($items['qty'])
				->setName($items['name'])
				->setSku($items['sku'])
				->setWeight(1)
				->setPrice($OrderedProductPriceWithEngrave)
				->setBasePrice($OrderedProductPriceWithEngrave)
				->setDiscountAmount($discountAmount)
				->setBaseDiscountAmount($discountAmount)
				// ->setDiscountPercent($LevelDiscountPercent)
				// ->setBaseDiscountPercent($LevelDiscountPercent)
				->setOriginalPrice($items['regularprice'])
				->setRowTotal($rowTotal)
				->setBaseRowTotal($rowTotal)
				->setBaseWeeeTaxDisposition(0)
				->setWeeeTaxDisposition(0)
				->setBaseWeeeTaxRowDisposition(0)
				->setWeeeTaxRowDisposition(0)
				->setBaseWeeeTaxAppliedAmount(0)
				->setBaseWeeeTaxAppliedRowAmount(0)
				->setWeeeTaxAppliedAmount(0)
				->setWeeeTaxAppliedRowAmount(0);
			$order->addItem($orderItem);
		}
		$subtotal = $totalOrderedProductPrice;
		// $subtract = $totalOrderedProductRegularPrice - $discountAmountTotal;
		// $subtotal = $subtract + $totalEngravePrice;
		// echo $subtotal . "\n";
		// echo "<pre>";
		// //exit;
		// // echo "new" . "\n";
		// // echo "engrave" . $subtotal . "\n";
		// print_r($super_attribute);

		// exit;
		$discount_amount = 0;
		// $subTotalWithEngrave = $subTotal + $totalEngravePrice;
		$grandTotal = $subtotal + $OrderShippingCosts;
		$order->setSubtotal($subtotal)
			->setBaseSubtotal($subtotal)
			->setShippingAmount($OrderShippingCosts)
			->setBaseShippingAmount($OrderShippingCosts)
			->setGrandTotal($grandTotal)
			->setDiscountAmount($discount_amount)
			->setBaseDiscountAmount($discount_amount)
			->setBaseGrandTotal($grandTotal);
		$order->setTotalPaid($grandTotal);
		$order->setBaseTotalPaid($grandTotal);
		/*
		 * BV_OP
		 * Set the order Incement Id same as the walmart purchase order Id
		 */
		$order->setIncrementId($OrderNumber);
		$order->setCreatedAt($authorizeon);
		// /* BV_OP end */
		if (!empty($csvdata["TransactionState"]) && $csvdata['TransactionState'] != '') {
			if ($csvdata['TransactionState'] == "CAPTURED") {
				$order->setStatus(\Magento\Sales\Model\Order::STATE_COMPLETE);
				$order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE);
			}
			if ($csvdata['TransactionState'] == "REFUNDED") {
				$order->setTotalRefunded($grandTotal);
				$order->setBaseTotalRefunded($grandTotal);
				$order->setStatus(\Magento\Sales\Model\Order::STATE_CLOSED);
				$order->setState(\Magento\Sales\Model\Order::STATE_CLOSED);
			}
		} else {
			$order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
			$order->setState(\Magento\Sales\Model\Order::STATE_NEW);
		}
		/* BV_OP end */

		$order->save();
		echo "increment_id" . $order->getIncrementId() . "\n";
		// echo "price" . $totalOrderedProductPrice . "\n";
		// //exit;
		// echo "regular" . $totalOrderedProductRegularPrice . "\n";
		// echo "discount" . $discountAmountTotal . "\n";
		echo "sheet_order_number" . $OrderNumber . "\n";

		//exit;
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/7-8-2019-create_new_order.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($order->getIncrementId());
	} catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
		echo $e->getMessage() . "\n";
		echo $OrderNumber . "\n";
	} catch (\Magento\Framework\Exception\LocalizedException $e) {
		echo $e->getMessage() . "\n";
		echo $OrderNumber . "\n";
	}

}

?>