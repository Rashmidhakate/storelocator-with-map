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
$fileUrl = 'new_product_sku.csv';
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
		$variation_sku = $csvdata['SKU'];
		$name = $csvdata['Name'];
		$size = trim($csvdata['size']);
		$width = trim($csvdata['Width']);
		$Price = $csvdata['Price'];
		$Category_Name = $csvdata['Category_Name'];
		$sku = $variation_sku . "_" . $size . "_" . $width;
		$dynamic_name = $name . "_" . $size . "_" . $width;
		$productModel = $obj->create('Magento\Catalog\Model\Product');
		try {

			$productModel = $obj->create('Magento\Catalog\Model\Product');
			$productBySku = $productModel->getIdBySku($variation_sku);
			if (!$productBySku) {
				$subCategory = $obj->create('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory')->create();
				$subCats = $subCategory->addAttributeToSelect('*');
				$subCats->addAttributeToFilter('is_active', array('in' => array(0, 1)));
				$subCats->addAttributeToFilter('parent_id', 364);
				foreach ($subCats as $subCat) {
					if ($subCat->getName() == $Category_Name) {
						$categoryArray = array(364, $subCat->getEntityId());
						break;
					}
				}
				// echo "<pre>";
				// print_r($categoryArray);
				// echo "\n";
				// echo "not exist";
				$configattr = array('size', 'width');
				$eavConfig = $obj->get('\Magento\Eav\Model\Config');
				$sizeAttribute = $eavConfig->getAttribute('catalog_product', 'size');
				$sizeOptions = $sizeAttribute->getSource()->getAllOptions();
				array_shift($sizeOptions);
				$sizeOptionsExists = array();

				foreach ($sizeOptions as $option) {
					if ($size == $option['label']) {
						$product = $obj->create(\Magento\Catalog\Model\Product::class);
						$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
							->setAttributeSetId(9)
							->setWebsiteIds([2])
							->setName($dynamic_name)
							->setSku($sku)
							->setPrice($Price)
							->setSize($option['value'])
							->setWeight(1)
							->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE)
							->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
							->setCategoryIds($categoryArray)
							->setStockData(
								array(
									'use_config_manage_stock' => 0,
									'manage_stock' => 1,
									'is_in_stock' => 1,
									'qty' => 1000,
								)
							);

						$product = $productRepository->save($product);
						break;
					}
				}

				$widthAttribute = $eavConfig->getAttribute('catalog_product', 'width');
				$widthOptions = $widthAttribute->getSource()->getAllOptions();
				array_shift($widthOptions);
				$widthOptionsExists = array();
				foreach ($widthOptions as $option) {
					if ($width == $option['label']) {
						$productobj = $obj->create('\Magento\Catalog\Model\Product');
						$productobj->load($product->getId());
						$productobj->setWidth($option['value']);
						$productobj->save();
						break;
					}
				}
				$msg = 'Created Simple Product. Product ID: ' . $productobj->getId();
				echo $msg . "\n";
				$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/create_variation_product.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info($productobj->getId());
			}
			//exit;
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