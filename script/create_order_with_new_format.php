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
$fileUrl = 'order_details.csv';

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
	$EngravePrice = $csvdata["EngravePrice"];
	//$RefundReason = $csvdata["RefundReason"];
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
	$itemsData["items"] = array();
	$option = array();
	try {
		foreach ($csvdata["items"] as $items) {
			$sku = explode(" ", $items["sku"]);
			$Quantity = $items["qty"];
			$ChosenSize = trim($items["size"]);
			$OrderedProductVariantName = trim($items["width"]);
			$OrderedProductSKU = $sku[0];
			$OrderedProductPrice = $items["price"];
			$config_product = $productRepository->get($OrderedProductSKU, true, 2);

			$price = $EngravePrice + $OrderedProductPrice;

			// $config_product->setPrice($OrderedProductPrice);
			// $config_product->setFinalPrice($OrderedProductPrice);
			// $config_product->save();
			// exit;
			$productModel = $obj->create('Magento\Catalog\Model\Product');
			$productModel->setStoreId(2);
			$productBySku = $productModel->getIdBySku($sku[0]);
			if ($productBySku) {

				foreach ($config_product->getOptions() as $o) {
					//print_r($o->getData());
					//$option[$o->getOptionId()] = "text engrave";
					if ($o->getTitle() == 'His Ring') {
						foreach ($o->getValues() as $value) {
							if ($value->getTitle() == $OrderedProductVariantName) {
								$option[$value->getOptionId()] = $value->getOptionTypeId();
								break;
							}

						}
					}
					if ($o->getTitle() == 'His Ring Size') {
						foreach ($o->getValues() as $value) {
							if ($value->getTitle() == $ChosenSize) {
								$option[$value->getOptionId()] = $value->getOptionTypeId();
								break;
							}

						}
					}
				}
				if ($option && $config_product->getPrice() > 0) {
					$itemsData["items"][] = [
						'product_id' => $config_product->getId(),
						'qty' => $Quantity,
						'options' => $option,
					];
					$price = $EngravePrice + $OrderedProductPrice;
					$config_product->setPrice($price);
					$config_product->setFinalPrice($price);
					$config_product->save();

					echo "main product" . $config_product->getPrice() . "\n";
				}
				if (!$option && $config_product->getPrice() == 0) {
					$_children = $obj->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable');
					$atrrCodes = array();
					foreach ($_children->getChildrenIds($config_product->getId()) as $child) {
						foreach ($child as $value) {
							$productModelChild = $obj->create('Magento\Catalog\Model\Product');
							$productModelChild->load($value);

							$widthlabel = trim($productModelChild->getResource()->getAttribute('width')->getFrontend()->getValue($productModelChild));
							//echo (int) $ChosenSize . "\n";
							$sizelabel = trim($productModelChild->getResource()->getAttribute('size')->getFrontend()->getValue($productModelChild));
							//echo (int) $sizelabel . "\n";
							// echo (int) $OrderedProductVariantName . "\n";
							//exit;
							$numaricWidth = str_split($OrderedProductVariantName);
							if ($ChosenSize == (int) $sizelabel && ($OrderedProductVariantName == $widthlabel || $numaricWidth[0] == $widthlabel)) {

								// echo $width;
								// echo $size;

								//print_r($numaricWidth[0]);
								//exit;
								$width = $productModelChild->getWidth();
								$size = $productModelChild->getSize();
								$atrrCodes = array(145 => $width, 147 => $size);
								$productModelChild->setPrice($price);
								$productModelChild->setFinalPrice($price);
								$productModelChild->save();
								echo "child product" . $productModelChild->getPrice() . "\n";
								break;
							}
						}
						//exit;
					}

					//print_r($atrrCodes);
					if (!$atrrCodes) {
						$itemsData["items"] = array();
					} else {
						$itemsData["items"][] = [
							'product_id' => $config_product->getId(),
							'selected_configurable_option' => $productModelChild->getId(),
							'qty' => $Quantity,
							'super_attribute' => $atrrCodes,
						];
					}
				}
			}
		}
		print_r($itemsData["items"]);
		// exit;
		if (!$itemsData["items"]) {
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/30-7-2019-order-not-created.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info($OrderNumber);
		} else {
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
			$addressData = [
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
			$orderData = array_merge($addressData, $itemsData);

			$store = $storeManager->getStore();
			$storeId = $store->getStoreId();
			$websiteId = $storeManager->getStore()->getWebsiteId();
			$customer = $custobjerFactory->create();
			$customer->setWebsiteId(2);
			$customer->loadByEmail($orderData['email']); // load customet by email address

			if (!$customer->getId()) {
				//For guest customer create new cusotmer
				$customer->setWebsiteId(2)
					->setStoreId(2)
					->setFirstname($orderData['shipping_address']['firstname'])
					->setLastname($orderData['shipping_address']['lastname'])
					->setEmail($orderData['email'])
					->setPassword('Demo@123');
				$customer->save();
			}
			$quote = $quoteFactory->create(); //Create object of quote
			$quote->setStoreId(2); //set store for our quote
			/* for registered customer */
			$customer = $custobjerRepository->getById($customer->getId());
			$quote->setCurrency();
			$quote->assignCustomer($customer); //Assign quote to customer
			foreach ($orderData['items'] as $item) {
				$storeId = $obj->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
				$product = $obj->create('Magento\Catalog\Model\Product')->setStoreId(2)->load($item['product_id']);
				if (!empty($item['super_attribute'])) {
					/* for configurable product */
					$buyRequest = new \Magento\Framework\DataObject($item);
					$quote->addProduct($product, $buyRequest);
				} else {
					/* for simple product */
					$quote->addProduct($product, intval($item['qty']));
				}
			}

			$quote->getBillingAddress()->addData($orderData['shipping_address']);
			$quote->getShippingAddress()->addData($orderData['billing_address']);

			// set shipping method
			$shippingAddress = $quote->getShippingAddress();

			$shippingAddress->setCollectShippingRates(true)
				->collectShippingRates()
				->setShippingMethod('customshipping_customshipping');
			//shipping method, please verify flat rate shipping must be enable

			//$quote->getShippingAddress()->addShippingRate(50);
			$quote->setPaymentMethod('newpayment'); //payment method, please verify checkmo must be enable from admin
			$quote->setInventoryProcessed(false); //decrease item stock equal to qty
			$quote->save(); //quote save
			// Set Sales Order Payment, We have taken check/money order
			// echo "<pre>";
			// print_r($quote->getData());
			// exit;
			$quote->getPayment()->importData(['method' => 'newpayment']);

			$order = $quoteManagement->submit($quote);

			//$notifier->notify(false);
			$order = $obj->create('Magento\Sales\Model\Order')
				->loadByAttribute('increment_id', $order->getIncrementId());
			$payment = $order->getPayment();
			$additionalInfo = array(
				'method_title' => $PaymentGateway,
			);
			foreach ($order->getAllItems() as $item) {
				$price = $EngravePrice + $OrderedProductPrice;
				$item->setPrice($price);
				$item->save();
			}

			$payment->setAdditionalInformation($additionalInfo);
			$order->setShippingDescription($ShippingMethod . "-" . $ShippingMethod);
			$order->setBaseShippingAmount($OrderShippingCosts);
			$order->setShippingAmount($OrderShippingCosts);
			$order->setSubtotal($OrderSubtotal);
			$order->setBaseSubtotal($OrderSubtotal);
			$subtotal = $order->getSubtotal() + $OrderShippingCosts;
			$order->setGrandTotal($subtotal);
			$order->setBaseGrandTotal($subtotal);
			$order->setCreatedAt($authorizeon);
			$order->setUpdatedAt($authorizeon);
			$order->save();
			echo "order Created" . "\n";
			//exit;
			if ($TransactionState == "CAPTURED") {
				if ($order->canShip()) {
					// Initialize the order shipment object
					$convertOrder = $obj->create('Magento\Sales\Model\Convert\Order');
					$shipment = $convertOrder->toShipment($order);
					// Loop through order items
					foreach ($order->getAllItems() AS $orderItem) {
						// Check if order item has qty to ship or is virtual
						if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
							continue;
						}
						$qtyShipped = $orderItem->getQtyToShip();
						// Create shipment item with qty
						$shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
						// Add shipment item to shipment
						$shipment->addItem($shipmentItem);
					}

					// Register shipment
					$shipment->register();
					$shipment->getOrder()->setIsInProcess(true);
					// Save created shipment and order
					$shipment->save();
					$shipment->getOrder()->save();

					echo "Shipment Succesfully Generated for order #" . "\n";
				} else {
					echo "Shipment Not Created Because It's already created or something went wrong";
				}
				if ($ShippingTrackingNumber) {

					$shipmentCollections = $order->getShipmentsCollection();
					foreach ($shipmentCollections as $shipment) {
						$shipmentId = $shipment->getEntityId();
						$shipmentIncrementId = $shipment->getIncrementId();
						$shipment = $shipmentCollection->loadByIncrementId($shipmentIncrementId);
						$shipment->setCreatedAt($shippon);
						$shipment->setUpdatedAt($shippon);
						$shipment->save();
						$track = $obj->create('Magento\Sales\Model\Order\Shipment\Track');
						$track->setOrderId($order->getId());
						$track->setParentId($shipmentId);
						$track->setTitle($ShippingMethod);
						$track->setCarrierCode($ShippingMethod);
						$track->setTrackNumber($ShippingTrackingNumber);
						$track->setQty(1);
						$track->setWeight(1);
						$track->setCreatedAt($shippon);
						$track->setUpdatedAt($shippon);
						$track->save();
						echo "shipment change datae" . "\n";

					}

				}
				echo "invoice generate" . "\n";
				if ($order->canInvoice()) {
					$invoice = $invoiceService->prepareInvoice($order);
					$invoice->setShippingAmount($OrderShippingCosts);
					$invoice->setSubtotal($order->getSubtotal());
					$invoice->setBaseSubtotal($order->getBaseSubtotal());
					$invoice->setGrandTotal($order->getGrandTotal());
					$invoice->setBaseGrandTotal($order->getBaseGrandTotal());
					$invoice->register();
					$invoice->save();
					$transactionSave = $transaction->addObject(
						$invoice
					)->addObject(
						$invoice->getOrder()
					);
					$transactionSave->save();
					//$invoiceSender->send($invoice);

					echo "invoicecollection" . "\n";

					$salesInvoiceChangeDate = "UPDATE `sales_invoice` SET `created_at` = '" . $captureon . "',`updated_at` = '" . $captureon . "' WHERE `order_id` = '" . $order->getId() . "'";
					$connection->query($salesInvoiceChangeDate);

					$salesInvoiceGridChangeDate = "UPDATE `sales_invoice_grid` SET `created_at` = '" . $captureon . "',`updated_at` = '" . $captureon . "' WHERE `order_id` = '" . $order->getId() . "'";
					$connection->query($salesInvoiceGridChangeDate);

					// echo $salesInvoiceChangeDate . "\n";
					// echo $salesInvoiceGridChangeDate . "\n";
					//exit;
					// $invoiceCollections = $order->getInvoiceCollection();
					// foreach ($invoiceCollections as $invoice) {
					// 	$invoiceIncrementID = $invoice->getIncrementId(); // invoice increment id
					// 	// same way get other details of invoice
					// 	$invoice = $invoiceCollection->loadByIncrementId($invoiceIncrementID);
					// 	$invoice->setCreatedAt($captureon);
					// 	$invoice->setUpdatedAt($captureon);
					// 	$invoice->save();
					// }
					// //send notification code
					$order->addStatusHistoryComment(
						__('Notified customer about invoice #%1.', $invoice->getId())
					)
						->setIsCustomerNotified(false)
						->save();
				}
				$order->setTotalPaid($order->getGrandTotal());
				$order->setBaseTotalPaid($order->getGrandTotal());
				$order->setTotalDue(0);
				$order->setBaseTotalDue(0);
				$order->save();
				echo "complete" . "\n";
				//exit;
				$historyOrderDate = "UPDATE `sales_order_status_history` SET `created_at` = '" . $authorizeon . "' WHERE `parent_id` = '" . $order->getId() . "'AND `entity_name` = 'order'";
				$connection->query($historyOrderDate);

				$historyInvoiceDate = "UPDATE `sales_order_status_history` SET `created_at` = '" . $captureon . "' WHERE `parent_id` = '" . $order->getId() . "'AND `entity_name` = 'invoice'";
				$connection->query($historyInvoiceDate);

				echo "order history" . "\n";

			}
			if ($TransactionState == "REFUNDED") {
				echo "false" . "\n";
				echo "invoice generate" . "\n";
				if ($order->canInvoice()) {
					$invoice = $invoiceService->prepareInvoice($order);
					$invoice->register();
					$invoice->save();
					$transactionSave = $transaction->addObject(
						$invoice
					)->addObject(
						$invoice->getOrder()
					);
					$transactionSave->save();
					//$invoiceSender->send($invoice);

					echo "invoicecollection" . "\n";

					$salesInvoiceChangeDate = "UPDATE `sales_invoice` SET `created_at` = '" . $captureon . "',`updated_at` = '" . $captureon . "' WHERE `order_id` = '" . $order->getId() . "'";
					$connection->query($salesInvoiceChangeDate);

					$salesInvoiceGridChangeDate = "UPDATE `sales_invoice_grid` SET `created_at` = '" . $captureon . "',`updated_at` = '" . $captureon . "' WHERE `order_id` = '" . $order->getId() . "'";
					$connection->query($salesInvoiceGridChangeDate);

					// echo $salesInvoiceChangeDate . "\n";
					// echo $salesInvoiceGridChangeDate . "\n";
					// $invoiceCollections = $order->getInvoiceCollection();
					// foreach ($invoiceCollections as $invoice) {
					// 	$invoiceIncrementID = $invoice->getIncrementId(); // invoice increment id

					// 	// same way get other details of invoice
					// 	$invoice = $invoiceCollection->loadByIncrementId($invoiceIncrementID);
					// 	$invoice->setCreatedAt($captureon);
					// 	$invoice->setUpdatedAt($captureon);
					// 	$invoice->save();
					// }
					// //send notification code
					$order->addStatusHistoryComment(
						__('Notified customer about invoice #%1.', $invoice->getId())
					)
						->setIsCustomerNotified(false)
						->save();
				}
				$invoices = $order->getInvoiceCollection();
				foreach ($invoices as $invoice) {
					$invoiceincrementid = $invoice->getIncrementId();
				}

				$invoiceobj = $invoice->loadByIncrementId($invoiceincrementid);
				$creditmemo = $creditmemoFactory->createByOrder($order);

				$creditmemo->setInvoice($invoiceobj);

				$creditmemoService->refund($creditmemo);

				$creditMemos = $obj->get('Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection');
				$creditMemos->addFieldToFilter('order_id', $order->getId());
				$creditMemos->load();

				foreach ($creditMemos as $creditMemo) {
					$creditMemo->setCreatedAt($refundon);
					$creditMemo->setUpdatedAt($refundon);
					$creditMemo->save();
				}
				echo "refund" . "\n";

				$historyOrderDate = "UPDATE `sales_order_status_history` SET `created_at` = '" . $authorizeon . "' WHERE `parent_id` = '" . $order->getId() . "'AND `entity_name` = 'order'";
				$connection->query($historyOrderDate);

				$historyInvoiceDate = "UPDATE `sales_order_status_history` SET `created_at` = '" . $captureon . "' WHERE `parent_id` = '" . $order->getId() . "'AND `entity_name` = 'invoice'";
				$connection->query($historyInvoiceDate);

				$historyCreditmemoDate = "UPDATE `sales_order_status_history` SET `created_at` = '" . $refundon . "' WHERE `parent_id` = '" . $order->getId() . "'AND `entity_name` = 'creditmemo'";
				$connection->query($historyCreditmemoDate);

				echo "order history" . "\n";

			}
		}
		echo $OrderNumber . "\n";
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/5-8-2019-order_create_new.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($OrderNumber);
		$logger->info("ordercount" . "=====>" . $orderCount);
		//exit;
	} catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
		echo $e->getMessage() . "\n";
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/5-8-2019-product_Not_available.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($OrderNumber);
		$logger->info("ordercount" . "=====>" . $orderCount);
		echo $OrderNumber . "\n";
	} catch (\Magento\Framework\Exception\LocalizedException $e) {
		echo $e->getMessage() . "\n";
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/5-8-2019-get_product_id_but_product_Not_available.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($OrderNumber);
		$logger->info("ordercount" . "=====>" . $orderCount);
		echo $OrderNumber . "\n";
	}

	// echo $OrderNumber;
	//exit;

	//}

}

?>