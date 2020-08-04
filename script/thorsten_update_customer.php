<?php

use Magento\Framework\App\Bootstrap;
require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$obj->get('Magento\Framework\Registry')->register('isSecureArea', true);

/* country collection */
$countryFactory = $obj->get('\Magento\Directory\Model\CountryFactory');

$country = $countryFactory->create();
$countryCollection = $country->getCollection();

/* end country collection */

/* database connection */

$resource = $obj->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();

/* end database connection */

$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');
$fileUrl = 'live_update_customer.csv';

$header = NULL;
$data = array();
if (($handle = @fopen($fileUrl, 'r')) !== FALSE) {
	while (($row = @fgetcsv($handle, 1000, ",")) !== FALSE) {
		if (!$header) {
			$header = $row;
		} else {
			$data[] = array_combine($header, $row);
		}

	}
	$customerCount = 0;
	foreach ($data as $csvdata) {
		$Email = $csvdata['Email'];
		$universal_account_number = $csvdata['universal_account_number'];

		$customerFactory = $obj->get('\Magento\Customer\Model\CustomerFactory');
		$customerDetail = $customerFactory->create();
		$customerDetail->setWebsiteId(3);
		

		$customer = $customerDetail->loadByEmail($Email);
		
		if (!$customer->getEntityId()) {
			echo "Updating customer..." . "\n";
		}
		else{
			echo "Creating new customer..." . "\n";
		}

		$customer->setStoreId(3);
		$customerData = $customer->getDataModel();

		//$customerData->setCustomAttribute('telephone', $Phone);
		$customerData->setCustomAttribute('universal_account_number', $universal_account_number);

		$customer->updateData($customerData);
		$customer->save();
		
		$customerCount++ . "\n";
		echo 'Total Customer : '. $customerCount . "\n";

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_15-01-2020.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($customer->getEmail());
	}

}
echo "hello";
exit;
?>