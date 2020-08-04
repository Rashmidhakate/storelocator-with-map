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
$fileUrl = 'configure_product_in_thorsten_list.csv';
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
		$sku = $csvdata['sku'];
		try {
			
			$productModel = $obj->create('Magento\Catalog\Model\Product');
			$productBySku = $productModel->getIdBySku($sku);
			if ($productBySku) {
				$productExistCount++;
				$productModel->load($productBySku);

				echo $productModel->getId() . "\n";
				$associateIds = array();
				$_children = $obj->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable');
				$data = $_children->getChildrenIds($productModel->getId());
				if(count($data[0]) > 0){
					foreach ($_children->getChildrenIds($productModel->getId()) as $child) {
						foreach ($child as $value) {
							$productModelChild = $obj->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory')->create();
							//$productModelChild->create();
							$productModelChild->addAttributeToSelect('*');
							$productModelChild->addAttributeToFilter('sku', array('nlike' => '%'.$sku.'%'));
							$productModelChild->addAttributeToFilter('entity_id', array('eq' => $value));
							$productModelChild->load();

							//echo $productModelChild->getEntityId()."\n";
							$productData = $productModelChild->getData();
							// echo "\n";
							// print_r($productData);
							if($productData){
								$associateIds[] = $productData[0]['entity_id'];
							}
						}
					}
					$array_data = array_filter($associateIds);
					//print_r(array_filter($associateIds))."\n";
					$final_result = implode(",",$array_data);
					// echo $final_result;
					//exit;
					if($final_result){
						$resource = $obj->get('Magento\Framework\App\ResourceConnection');
						$connection = $resource->getConnection();
						$sql = "Delete FROM catalog_product_super_link Where parent_id ='".$productModel->getId()."' AND product_id IN (".$final_result.")";

						//echo $sql;
						$connection->query($sql);
						//exit;

						$msg = 'Updated Configurable Product. Product ID: ' . $productModel->getId();
						echo "Updated" . $productExistCount . "\n";
						echo $msg . "\n";

						$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/4-10-2019_delete_extra_sku_configurable.log');
						$logger = new \Zend\Log\Logger();
						$logger->addWriter($writer);
						$logger->info($productModel->getId());
					}
				}
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