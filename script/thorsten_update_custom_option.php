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
$fileUrl = 'related_sku_for_configurable_product.csv';

$resource = $obj->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();

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
	$categoryCount = 0;
	$sourceItems = [];
	foreach ($data as $csvdata) {
		$sku = $csvdata['SKU'];
		$categoryCount++;
		echo $sku."\n";

		$changeCustomOptionValue = "UPDATE `catalog_product_entity` SET `has_options` = '1',`required_options` = '1' WHERE `sku` = '".$sku."'";
		//echo $changeCustomOptionValue;
		$connection->query($changeCustomOptionValue);
		echo $categoryCount."\n";
		//exit;
	}
echo "hello";
exit;
}
?>