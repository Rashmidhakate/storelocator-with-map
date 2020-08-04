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
$fileUrl = '14-gold-inlays.csv';
$product = $obj->create('\Magento\Catalog\Model\Product');
$productRepository = $obj->get('Magento\Catalog\Api\ProductRepositoryInterface');
$header = NULL;
$data = array();
$categoryCount = 0;
if (($handle = @fopen($fileUrl, 'r')) !== FALSE) {
	while (($row = @fgetcsv($handle, 10000, ",")) !== FALSE) {
		if (!$header) {
			$header = $row;
		} else {
			$data[] = array_combine($header, $row);
		}

	}
	$categoryCount = 0;
	$sourceItems = [];
	$CategoryLinkRepository = $obj->get('\Magento\Catalog\Model\CategoryLinkRepository');
	foreach ($data as $csvdata) {
		$categoryCount++;
		$variation_sku = $csvdata['SKU'];
		$productModel = $obj->create('Magento\Catalog\Model\Product');
		// $productModel->getCollection();
		// $productModel->addAttributeToSelect("*");
		// $productModel->addAttributeToFilter('website_ids', 2);
		// echo $productModel->getSize();
		//exit;
		$productBySku = $productModel->getIdBySku($variation_sku);
		if ($productBySku) {

			$productCollection = $obj->create('\Magento\Catalog\Api\ProductRepositoryInterface');

			$collection = $productCollection->get($variation_sku);
			echo $categoryCount . "\n";
			if (!in_array(2, $collection->getwebsiteIds())) {

				echo "not exist" . "\n";
				$collection->setWebsiteIds([2]);
			} else {
				echo "exist" . "\n";
				$collection->setCategoryIds(array(489));
				$collection->save();
				//exit;
				$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/new-style-ring-available-products.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info($collection->getId());
				$logger->info($collection->getSku());
			}
		} else {
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/new-style-ring-not-available-products.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info($variation_sku);
		}

	}
}

?>