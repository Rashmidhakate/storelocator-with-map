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
$fileUrl = 'manufacture_not_exist.csv';
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
	$categoryCount = 2091942531;
	$sourceItems = [];
	foreach ($data as $csvdata) {
		$totalrecords++;
		echo $totalrecords . "\n";
		$variation_sku = $csvdata['entity_id'];

		//$name = $csvdata['Name'];
		$manufacturer = trim($csvdata['manufacturer']);
		//echo $manufacturer."\n";
		//$width = trim($csvdata['Width']);
		//$Price = $csvdata['Price'];
		//$Category_Name = $csvdata['Category_Name'];
		// $sku = $variation_sku . "_" . $size . "_" . $width;
		// $dynamic_name = $name . "_" . $size . "_" . $width;
		//$productModel = $obj->create('Magento\Catalog\Model\Product');
		try {
			$resource = $obj->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			$tableName = $resource->getTableName('catalog_product_entity_int'); //gives table name with prefix
			$sql = "SELECT entity_id FROM `catalog_product_entity_int` WHERE attribute_id = 83 AND entity_id=".$variation_sku;
			$result = $connection->fetchAll($sql);
			if(!$result){
				$categoryCount+=1;
				$sql = "INSERT INTO `catalog_product_entity_int` (`value_id`, `attribute_id`, `store_id`, `entity_id`, `value`) Values ($categoryCount, '83', '0', $variation_sku, '72')";
				$connection->query($sql);
			}
			
			// $productModel = $obj->create('Magento\Catalog\Model\Product');
			// // $productBySku = $productModel->getIdBySku($variation_sku);
			// // if ($productBySku) {
			// 	$eavConfig = $obj->get('\Magento\Eav\Model\Config');
			// 	$sizeAttribute = $eavConfig->getAttribute('catalog_product', 'manufacturer');
			// 	$sizeOptions = $sizeAttribute->getSource()->getAllOptions();
			// 	array_shift($sizeOptions);
			// 	$productModel->load($variation_sku);
			// 	foreach ($sizeOptions as $option) {
			// 		if ($manufacturer == $option['label']) {
			// 			echo $option['value']."\n";
			// 			$productModel->setManufacturer($option['value']);
			// 			$productModel->save();
			// 			break;
			// 		}
			// 	}
				// $msg = 'Updated Product. Product ID: ' . $productModel->getId();
				echo $variation_sku . "\n";
				//exit;
			//}
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