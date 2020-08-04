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
$fileUrl = 'category.csv';

$rootCat = $obj->get('Magento\Catalog\Model\Category');
$cat_info = $rootCat->load(364);

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
		$store = $storeManager->getStore();
		$storeId = $store->getStoreId();
		$name = $csvdata['Name'];
		$description = $csvdata['Description'];
		$metaKeywords = $csvdata['SEKeywords'];
		$metaDescription = $csvdata['SEDescription'];
		$metaTitle = $csvdata['SETitle'];
		$catName = ucfirst($name);
		$url = strtolower($catName);
		$urlKey = str_replace(' ', '-', $url);
		echo $urlKey;
		$categoryFactory = $obj->get('\Magento\Catalog\Model\CategoryFactory');
		/// Add a new sub category under root category
		$categoryTmp = $categoryFactory->create();

		// echo "<pre>";
		// print_r($categoryTmp->getData());
		$categoryTmp->setName($name);
		$categoryTmp->setIsActive(false);
		$categoryTmp->setUrlKey($urlKey);
		$categoryTmp->setDescription($description);
		$categoryTmp->setMetaKeywords($metaKeywords);
		$categoryTmp->setMetaDescription($metaDescription);
		$categoryTmp->setMetaTitle($metaTitle);
		$categoryTmp->setParentId($rootCat->getId());
		$categoryTmp->setStoreId(0);
		$categoryTmp->setPath($rootCat->getPath());
		$categoryTmp->save();

		$categoryCount++ . "\n";
		echo $categoryCount . " successfully created";

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/category_exists.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($name);
	}

}
echo "hello";
exit;
?>