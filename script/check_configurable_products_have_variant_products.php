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
$fileUrl = 'thorstenring_category_unique.csv';
$product = $obj->create('\Magento\Catalog\Model\Product');
$productRepository = $obj->get('Magento\Catalog\Api\ProductRepositoryInterface');
$optionFactory = $obj->get('\Magento\Catalog\Model\Product\Option');
$header = NULL;
$data = array();
$associatedProductIds = array();
$sizeProductOptions = array();
$widthProductOptions = array();
$totalrecords = 0;
$productExistCount = 0;
$productNotExistCount = 0;
if (($handle = @fopen($fileUrl, 'r')) !== FALSE) {
	while (($row = @fgetcsv($handle, 10000, ",")) !== FALSE) {
		if (!$header) {
			$header = $row;
		} else {
			$data[] = array_combine($header, $row);
		}

	}
	$sourceItems = [];
	$categoryArray = [];
	foreach ($data as $csvdata) {
		$totalrecords++;
		echo $totalrecords . "\n";
		$sku = $csvdata['SKU'];
		$category = $csvdata['Category'];
		try {
			$subCategory = $obj->create('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory')->create();
			$subCats = $subCategory->addAttributeToSelect('*');
			$subCats->addAttributeToFilter('is_active', array('in' => array(0, 1)));
			$subCats->addAttributeToFilter('parent_id', 364);
			foreach ($subCats as $subCat) {
				if ($subCat->getName() == $category) {
					$categoryArray = array(364, $subCat->getEntityId());
					break;
				}
			}
			echo "<pre>";
			print_r($categoryArray);
			//print_r($subCats->getData());
			//exit;
			$productModel = $obj->create('Magento\Catalog\Model\Product');
			$productBySku = $productModel->getIdBySku($sku);
			if ($productBySku) {
				$productExistCount++;
				$productModel->load($productBySku);

				echo $productModel->getId() . "\n";
				$associateIds = array();
				$_children = $obj->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable');
				foreach ($_children->getChildrenIds($productModel->getId()) as $child) {
					foreach ($child as $value) {
						$productModelChild = $obj->create('Magento\Catalog\Model\Product');
						$productModelChild->load($value);
						$productModelChild->setWeight(1);
						$productModelChild->setWebsiteIds([1, 2]);
						$productModelChild->setCategoryIds($categoryArray);
						$productModelChild->save();
						echo $value . "\n";
					}
				}
				print_r($child);

				$customOptions = $obj->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($productModel);
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

					$productModel->setHasOptions(1);
					$productModel->setCanSaveCustomOptions(true);
					foreach ($options as $arrayOption) {
						$option = $obj->create('\Magento\Catalog\Model\Product\Option')
							->setProductId($productModel->getId())
							->setStoreId($productModel->getStoreId())
							->addData($arrayOption);
						$option->save();
						$productModel->addOption($option);
					}

				}

				$productModel->setWebsiteIds([1, 2]);
				$productModel->setCategoryIds($categoryArray);
				$productModel->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
					->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
					->setStockData(array(
						'use_config_manage_stock' => 0, //'Use config settings' checkbox
						'manage_stock' => 1, //manage stock
						'is_in_stock' => 1, //Stock Availability
					)
					);
				// $productModel->setAssociatedProductIds($child); // Setting Associated Products
				// $productModel->setCanSaveConfigurableAttributes(true);
				$productModel->save();

				$msg = 'Updated Configurable Product. Product ID: ' . $productModel->getId();
				echo "Updated" . $productExistCount . "\n";
				echo $msg . "\n";

				$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/17-7-2019_counfigurable_product.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info($productModel->getId());
			}

			echo $totalrecords . "\n";

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

		//exit;

	}

}

?>