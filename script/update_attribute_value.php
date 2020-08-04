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
$fileUrl = 'Larson_manufacture.csv';
$product = $obj->create('\Magento\Catalog\Model\Product');
$productRepository = $obj->get('Magento\Catalog\Api\ProductRepositoryInterface');
$header = NULL;
$data = array();
$totalrecords = 0;
if (($handle = @fopen($fileUrl, 'r')) !== FALSE) {
	while (($row = @fgetcsv($handle, 100000, ",")) !== FALSE) {
		if (!$header) {
			$header = $row;
		} else {
			$data[] = array_combine($header, $row);
		}

	}
	$categoryCount = 0;
	$sourceItems = [];
	foreach ($data as $csvdata) {
		$totalrecords++;
		echo $totalrecords . "\n";
		$variation_sku = $csvdata['sku'];

		//$name = $csvdata['Name'];
		$manufacturer = trim($csvdata['manufacturer']);
		//echo $manufacturer."\n";
		//$width = trim($csvdata['Width']);
		//$Price = $csvdata['Price'];
		//$Category_Name = $csvdata['Category_Name'];
		// $sku = $variation_sku . "_" . $size . "_" . $width;
		// $dynamic_name = $name . "_" . $size . "_" . $width;
		$productModel = $obj->create('Magento\Catalog\Model\Product');
		try {

			$productModel = $obj->create('Magento\Catalog\Model\Product');
			$productBySku = $productModel->getIdBySku($variation_sku);
			if ($productBySku) {
				$eavConfig = $obj->get('\Magento\Eav\Model\Config');
				$sizeAttribute = $eavConfig->getAttribute('catalog_product', 'manufacturer');
				$sizeOptions = $sizeAttribute->getSource()->getAllOptions();
				array_shift($sizeOptions);
				$productModel->load($productBySku);
				foreach ($sizeOptions as $option) {
					if ($manufacturer == $option['label']) {
						$productModel->setManufacturer($option['value']);
						$productModel->save();
						break;
					}
				}
				$msg = 'Updated Product. Product ID: ' . $productModel->getId();
				echo $msg . "\n";
			}
		} catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
			echo $e->getMessage();
		} catch (\Magento\Framework\Exception\InputException $e) {
			echo $e->getMessage();
		} catch (\Magento\Eav\Model\Entity\Attribute\Exception $e) {
			echo $e->getMessage();
		} catch (\Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException $e) {
			echo $e->getMessage();
		} catch (\Zend_Db_Statement_Exception $e) {
			echo $e->getMessage();
		}
	}
}
?>