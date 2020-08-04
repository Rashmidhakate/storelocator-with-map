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
$fileUrl = 'configure_product_latest.csv';
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
		$sku = $csvdata['SKU'];
		$name = $csvdata['Name'];
		$Price = $csvdata['Price'];
		$Cost = $csvdata['Cost'];
		$Category = $csvdata['Category'];
		//$url_key = $csvdata['SEName'];
		$Thickness = $csvdata['Thickness'];
		$Weight = $csvdata['Weight'];
		$description = $csvdata['Description'];
		$short_description = $csvdata['Summary'];
		$meta_title = $csvdata['SETitle'];
		$meta_keywords = $csvdata['SEKeywords'];
		$meta_description = $csvdata['SEDescription'];
		$engraving = $csvdata['Is Engraved'];
		$taxClassId = $csvdata['Tax Class ID'];
		$result = strtolower(preg_replace("/[\s-]+/", "-", $name));
		$result_sku = strtolower($sku);
		$url_key = $result . "-" . $result_sku;
		//echo $url_key . "\n";

		try {

			$productModel = $obj->create('Magento\Catalog\Model\Product');
			// echo "<pre>";
			// print_r(get_class($productModel));
			$productBySku = $productModel->getIdBySku($sku);
			// echo "product_id";
			// echo $productBySku . "\n";
			//exit;
			if (!$productBySku) {
				$subCategory = $obj->create('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory')->create();
				$subCats = $subCategory->addAttributeToSelect('*');
				$subCats->addAttributeToFilter('is_active', array('in' => array(0, 1)));
				$subCats->addAttributeToFilter('parent_id', 364);
				foreach ($subCats as $subCat) {
					if ($subCat->getName() == $Category) {
						$categoryArray = array(364, $subCat->getEntityId());
						break;
					}
				}
				// echo "<pre>";
				// print_r($categoryArray);

				$productobj = $obj->create('\Magento\Catalog\Model\ProductFactory');
				$search = $sku . "_" . "%";
				$collection = $productobj->create()->getCollection();
				$collection->addAttributeToSelect('*');
				$collection->addAttributeToFilter('sku', array('like' => $search));
				$collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

				foreach ($collection as $product) {
					$associatedProductIds[] = $product->getId();
					$sizeProductOptions[] = $product->getSize();
					$widthProductOptions[] = $product->getWidth();
				}

				$configattr = array('size', 'width');
				$eavConfig = $obj->get('\Magento\Eav\Model\Config');
				$sizeAttribute = $eavConfig->getAttribute('catalog_product', 'size');
				$sizeOptions = $sizeAttribute->getSource()->getAllOptions();
				array_shift($sizeOptions);
				$sizeOptionsExists = array();

				foreach ($sizeOptions as $option) {
					if (in_array($option['value'], $sizeProductOptions)) {
						$sizeOptionsExists[$option['value']] = [
							'label' => $option['label'],
							'attribute_id' => $sizeAttribute->getId(),
							'value_index' => $option['value'],
							'include' => 1,
						];
					}
				}

				$widthAttribute = $eavConfig->getAttribute('catalog_product', 'width');
				$widthOptions = $widthAttribute->getSource()->getAllOptions();
				array_shift($widthOptions);
				$widthOptionsExists = array();
				//$count = 0;
				foreach ($widthOptions as $option) {
					if (in_array($option['value'], $widthProductOptions)) {
						$widthOptionsExists[$option['value']] = [
							'label' => $option['label'],
							'attribute_id' => $sizeAttribute->getId(),
							'value_index' => $option['value'],
							'include' => 1,
						];
					}
				}

				$configurableSizeAttributesData[$sizeAttribute->getId()] =
					[
					'attribute_id' => $sizeAttribute->getId(),
					'code' => $sizeAttribute->getAttributeCode(),
					'label' => $sizeAttribute->getStoreLabel(),
					'position' => '0',
					'values' => $sizeOptionsExists,
				];

				$configurablewidthAttributesData[$widthAttribute->getId()] =
					[
					'attribute_id' => $widthAttribute->getId(),
					'code' => $widthAttribute->getAttributeCode(),
					'label' => $widthAttribute->getStoreLabel(),
					'position' => '0',
					'values' => $widthOptionsExists,
				];

				$configurableAttributesData = array_merge($configurableSizeAttributesData, $configurablewidthAttributesData);

				// echo "<pre>";
				// print_r($associatedProductIds);
				//exit;

				$configurableAttributesData = array_merge($configurableSizeAttributesData, $configurablewidthAttributesData);

				$productObj = $obj->create(\Magento\Catalog\Model\Product::class);
				$optionsFactory = $obj->create(\Magento\ConfigurableProduct\Helper\Product\Options\Factory::class);
				$configurableOptions = $optionsFactory->create($configurableAttributesData);

				$extensionConfigurableAttributes = $productObj->getExtensionAttributes();
				$extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
				$extensionConfigurableAttributes->setConfigurableProductLinks($associatedProductIds);
				$productObj->setExtensionAttributes($extensionConfigurableAttributes);
				echo "set configure product" . "\n";
				$productObj->setTypeId(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
					->setAttributeSetId(9)
					->setWebsiteIds([2])
					->setCategoryIds($categoryArray)
					->setName($name)
					->setSku($sku)
					->setPrice($Price)
				//->setCost($Cost)
					->setUrlKey($url_key)
					->setThickness($Thickness)
					->setDescription($description)
					->setShortDescription($short_description)
					->setMetaTitle($meta_title)
					->setMetaKeywords($meta_keywords)
					->setMetaDescription($meta_description)
					->setEngraving($engraving)
					->setWeight($Weight)
					->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
					->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
					->setStockData(array(
						'use_config_manage_stock' => 0, //'Use config settings' checkbox
						'manage_stock' => 1, //manage stock
						'is_in_stock' => 1, //Stock Availability
					)
					);
				$productObj = $productRepository->save($productObj);
				//echo $productObj->getId() . "\n";
				//echo "set configure product with custom option" . "\n";
				$productModel = $obj->create('\Magento\Catalog\Model\ProductFactory');
				$product = $productModel->create()->load($productObj->getId());
				echo $product->getId() . "\n";
				//echo "set configure product with custom option" . "\n";
				$customOptions = $obj->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($product);
				if (count($customOptions) == 0) {
					$values = [
						[
							'record_id' => 0,
							'title' => 'Text Engraving',
							'price' => 25,
							'price_type' => "fixed",
							'sort_order' => 1,
							'is_delete' => 0,
						],
						[
							'record_id' => 1,
							'title' => 'Handwritten Engraving',
							'price' => 50,
							'price_type' => "fixed",
							'sort_order' => 2,
							'is_delete' => 0,
						],
						[
							'record_id' => 2,
							'title' => 'Fingerprint Engraving',
							'price' => 50,
							'price_type' => "fixed",
							'sort_order' => 3,
							'is_delete' => 0,
						],
						[
							'record_id' => 3,
							'title' => 'Custom Engraving',
							'price' => 50,
							'price_type' => "fixed",
							'sort_order' => 4,
							'is_delete' => 0,
						],
					];

					$options = [
						[
							"sort_order" => 1,
							"title" => "Engraving",
							"price_type" => "fixed",
							"price" => "",
							"type" => "drop_down",
							"is_require" => 0,
							"values" => $values,
						], [
							"sort_order" => 2,
							"title" => "Engraving Message",
							"price_type" => "fixed",
							"price" => "",
							"type" => "area",
							"is_require" => 0,
						],
					];

					$product->setHasOptions(1);
					$product->setCanSaveCustomOptions(true);
					foreach ($options as $arrayOption) {
						$option = $obj->create('\Magento\Catalog\Model\Product\Option')
							->setProductId($product->getId())
							->setStoreId($product->getStoreId())
							->addData($arrayOption);
						$option->save();
						$product->addOption($option);
					}

				}
				$product->save();
				$msg = 'Created Configurable Product. Product ID: ' . $product->getId();
				echo $msg . "\n";
				$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/create_new_configurable_22_7_2019.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info($product->getId());
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