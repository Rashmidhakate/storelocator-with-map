<?php 

use Magento\Framework\App\Bootstrap;
require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$objectManager = $bootstrap->getObjectManager();

$fileUrl = 'configurable_product_in_both_site.csv'; 


// $state = $objectManager->get('Magento\Framework\App\State');
// $state->setAreaCode('frontend');

$header = NULL;
$data = array();
	if (($handle = @fopen($fileUrl, 'r')) !== FALSE)
	{
		while (($row = @fgetcsv($handle, 1000, ",")) !== FALSE)
		{
			if(!$header)
				$header = $row;
			else
				$data[] = array_combine($header, $row);
		}
		$productExistCount = 0;
		$productNotExistCount = 0;
		$sourceItems = [];
		foreach ($data as $csvdata) {
			$dot_sku = $csvdata['sku'];
			// $image1 = explode(',',$csvdata['images']);
			// $result = str_replace('-', '', $dot_sku);
			
			$productModel = $objectManager->create('Magento\Catalog\Model\ProductFactory');
			$productBySku = $productModel->create()->getIdBySku($dot_sku);
			if($productBySku) {
				
				echo $productBySku."exist </br>";
				//if(sizeof($image1) > 0) {
					$product = $productModel->create()->load($productBySku);
					$productRepository = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');
					$existingMediaGalleryEntries = $product->getMediaGalleryEntries();
					if(!$existingMediaGalleryEntries){
						$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/remaning_product_image_not_available_4_10_2019.log');
						$logger = new \Zend\Log\Logger();
						$logger->addWriter($writer);
						$logger->info($product->getSku());
						$productExistCount++;
					}
					// foreach ($existingMediaGalleryEntries as $key => $entry) {
					// 	unset($existingMediaGalleryEntries[$key]);
					// }
					// $product->setMediaGalleryEntries($existingMediaGalleryEntries);
					// $productRepository->save($product);
					
					// $filespath = $objectManager->get('\Magento\Framework\Filesystem');
					// $prdbasepath = $filespath->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();

					

				//}
				

			}else{
				echo $productBySku."not exist";

			}

		}
		echo $productExistCount." successfully created";
	}
echo "hello";
?>