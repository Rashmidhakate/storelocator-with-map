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
		$Related_Sku = $csvdata['Related_Sku'];

		try {

			$productModel = $obj->create('Magento\Catalog\Model\Product');
			$productBySku = $productModel->getIdBySku($sku);
			if ($productBySku) {

				$productModel = $obj->create('\Magento\Catalog\Model\ProductFactory');
				$product = $productModel->create()->load($productBySku);
				echo $product->getId() . "\n";
				$linkDataAll = [];
				//$skuLinks = "PR-1,PR-2,PR3,config-1";
				$skuLinks = explode(",", $Related_Sku);
				//print_r(array_filter($skuLinks));
				foreach ($skuLinks as $skuLink) {
					//check first that the product exist
					$linkedProduct = $product->loadByAttribute("sku", $skuLink);
					if ($linkedProduct) {
						/** @var  \Magento\Catalog\Api\Data\ProductLinkInterface $productLinks */
						$productLinks = $obj->create('Magento\Catalog\Api\Data\ProductLinkInterface');
						$linkData = $productLinks //Magento\Catalog\Api\Data\ProductLinkInterface
						->setSku($product->getSku())
							->setLinkedProductSku($skuLink)
							->setLinkType("related");
						$linkDataAll[] = $linkData;
					}

				}
				if ($linkDataAll) {
					$product->setProductLinks($linkDataAll);
				}
				$product->save();
				$msg = 'Created Configurable Product. Product ID: ' . $product->getId();
				echo $msg . "\n";
				$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/configurable_product_with_related_sku.log');
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