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
$fileUrl = 'address_not_available_for_customer.csv';

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
		echo "<pre>";
		// print_r($csvdata);
		// exit;
		$Email = $csvdata['Email'];
		$FirstName = $csvdata['FirstName'];
		$LastName = $csvdata['LastName'];
		$DateOfBirth = $csvdata['DateOfBirth'];
		$Gender = $csvdata['Gender'];
		$Phone = $csvdata['Phone'];
		$FAX = $csvdata['FAX'];
		$CODNet30Allowed = $csvdata['CODNet30Allowed'];
		$OkToEmail = $csvdata['OkToEmail'];
		$CompanyWebsite = $csvdata['CompanyWebsite'];
		$TypeOfStore = $csvdata['TypeOfStore'];
		$TotalStores = $csvdata['TotalStores'];
		$HearAboutUsBy = $csvdata['HearAboutUsBy'];
		$net30 = "net30";

		$Billing_Address1 = $csvdata['Billing_Address1'];
		$Billing_Address2 = $csvdata['Billing_Address2'];
		$Billing_City = $csvdata['Billing_City'];
		$Billing_State = $csvdata['Billing_State'];
		$Billing_Zip = $csvdata['Billing_Zip'];
		$Billing_Country = $csvdata['Billing_Country'];
		$Billing_Phone2 = $csvdata['Billing_Phone2'];
		$Shipping_Address1 = $csvdata['Shipping_Address1'];
		$Shipping_Address2 = $csvdata['Shipping_Address2'];
		$Shipping_City = $csvdata['Shipping_City'];
		$Shipping_State = $csvdata['Shipping_State'];
		$Shipping_Zip = $csvdata['Shipping_Zip'];
		$Shipping_Country = $csvdata['Shipping_Country'];
		$Shipping_Phone = $csvdata['Shipping_Phone'];

		/* check string is url or not */

		$regex = "((https?|ftp)\:\/\/)?"; // SCHEME
		$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
		$regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
		$regex .= "(\:[0-9]{2,5})?"; // Port
		$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor

		/* end check string is url or not */

		/* get option id of customer attribute using label */

		$tableName = $resource->getTableName('eav_attribute_option_value');

		$HearAboutUsBy = "SELECT option_id FROM `eav_attribute_option_value` WHERE `value` = '" . trim($HearAboutUsBy) . "'  ";
		$result_HearAboutUsBy = $connection->fetchOne($HearAboutUsBy);
		echo $result_HearAboutUsBy . "\n";
		$TypeOfStore = "SELECT option_id FROM `eav_attribute_option_value` WHERE `value` = '" . trim($TypeOfStore) . "'  ";
		$result_TypeOfStore = $connection->fetchOne($TypeOfStore);
		echo $result_TypeOfStore . "\n";
		$net30 = "SELECT option_id FROM `eav_attribute_option_value` WHERE `value` = '" . trim($net30) . "'  ";
		$result_net30 = $connection->fetchOne($net30);
		echo $result_net30 . "\n";
		//exit;
		/* end get option id of customer attribute using label */
		$customerFactory = $obj->get('\Magento\Customer\Model\CustomerFactory');

		$customerDetail = $customerFactory->create();
		// $customerCollection = $customerFactory->getCollection();
		// $customer = $customerCollection->load(100989);
		//echo "<pre>";set net term allowed 374 - no, 375 - yes, 376 - net30

		//$customerDetail = $customerFactory->create();

		$customerDetail->setWebsiteId(2);

		$customer = $customerDetail->loadByEmail($Email);
		// echo "<pre>";
		// print_r($customer->getData());
		// exit;
		if (!$customer->getEntityId()) {
			echo "not exist" . "\n";
			$customer->setWebsiteId(2);

			$customer->setStoreId(2);

			$customer->setEmail($Email);

			$customer->setFirstname($FirstName);

			$customer->setLastname($LastName);

			$customer->setPassword("Demo@123");

			if (isset($DateOfBirth)) {
				$customer->setDob($DateOfBirth);
			}

			if (isset($Gender)) {
				$customer->setGender($Gender);
			}
			$customerData = $customer->getDataModel();

			$customerData->setCustomAttribute('telephone', $Phone);

			if ($CODNet30Allowed == 1) {
				$customerData->setCustomAttribute('net_terms_allowed', 418);
				$customerData->setCustomAttribute('customer_net_terms', $result_net30);
			}

			$customerData->setCustomAttribute('ok_to_email', $OkToEmail);

			if (preg_match("/^$regex$/i", $CompanyWebsite)) // `i` flag for case-insensitive
			{
				$customerData->setCustomAttribute('company_website', $CompanyWebsite);
			} else {
				$customerData->setCustomAttribute('company', $CompanyWebsite);
			}

			$customerData->setCustomAttribute('type_of_store', $result_TypeOfStore);

			$customerData->setCustomAttribute('how_many_stores', $TotalStores);

			$customerData->setCustomAttribute('hear_abt_us', $result_HearAboutUsBy);

			$customer->updateData($customerData);
			$customer->save();
			/*
			if ($Billing_Address1 || $Billing_Address2 || $Billing_City || $Billing_State || $Billing_Zip || $Billing_Country || $Billing_Phone2
			) {
			echo $customer->getEntityId() . "\n";
			$addresss = $obj->get('\Magento\Customer\Model\AddressFactory');

			$address = $addresss->create();

			$address->setCustomerId($customer->getEntityId())

			->setFirstname($FirstName)

			->setLastname($LastName);

			/* get country code by country name

			foreach ($countryCollection as $country) {
			if ($Billing_Country == $country->getName()) {
			$billingCountryId = $country->getCountryId();
			break;
			}
			}
			$address->setCountryId($billingCountryId);
			echo $billingCountryId . "\n";
			//exit;
			/* end get country code by country name */

			/*check for country id , region available in database or not

			$billingRegionId = "SELECT region_id FROM `directory_country_region` WHERE `country_id` = '" . trim($billingCountryId) . "' AND `code` = '" . trim($Billing_State) . "' ";
			$result_billingRegionId = $connection->fetchOne($billingRegionId);

			if ($result_billingRegionId) {
			$address->setRegionId($result_billingRegionId);
			} else {
			$address->setRegion($Billing_State);
			}

			/* end check for country id , region available in database or not

			$address->setPostcode($Billing_Zip)

			->setCity($Billing_City)

			->setTelephone($Billing_Phone2);

			if (!preg_match("/^$regex$/i", $CompanyWebsite)) // `i` flag for case-insensitive
			{
			$address->setCompany($CompanyWebsite);
			}

			$address->setStreet($Billing_Address1)
			->setIsDefaultBilling('1')
			->setIsDefaultShipping(false)
			->setSaveInAddressBook('1');
			$address->save();

			}

			if ($Shipping_Address1 || $Shipping_Address2 || $Shipping_City || $Shipping_State || $Shipping_Zip || $Shipping_Country || $Shipping_Phone
			) {
			echo "shipping" . "\n";
			$addresss = $obj->get('\Magento\Customer\Model\AddressFactory');

			$address = $addresss->create();
			$address->setCustomerId($customer->getEntityId())

			->setFirstname($FirstName)

			->setLastname($LastName);

			/* get country code by country name
			foreach ($countryCollection as $country) {
			if ($Shipping_Country == $country->getName()) {
			$shippingCountryId = $country->getCountryId();
			break;
			}
			}
			$address->setCountryId($shippingCountryId);
			//echo $shippingCountryId;
			//exit;
			/* end get country code by country name

			/*check for country id , region available in database or not

			$shippingRegionId = "SELECT region_id FROM `directory_country_region` WHERE `country_id` = '" . trim($shippingCountryId) . "'  AND `code` = '" . trim($Shipping_State) . "'  ";
			$result_shippingRegionId = $connection->fetchOne($shippingRegionId);

			if ($result_shippingRegionId) {
			$address->setRegionId($result_shippingRegionId);
			} else {
			$address->setRegion($Shipping_State);
			}

			/* end check for country id , region available in database or not

			$address->setPostcode($Shipping_Zip)

			->setCity($Shipping_City)

			->setTelephone($Shipping_Phone);

			if (!preg_match("/^$regex$/i", $CompanyWebsite)) // `i` flag for case-insensitive
			{
			$address->setCompany($CompanyWebsite);
			}

			$address->setStreet($Shipping_Address1)
			->setIsDefaultBilling(false)
			->setIsDefaultShipping('1')
			->setSaveInAddressBook('1');
			$address->save();
			}*/

			$customerCount++ . "\n";
			echo $customerCount . "\n";

			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_without_address_19_7_2019.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info($customer->getEmail());
		}
	}

}
echo "hello";
exit;
?>