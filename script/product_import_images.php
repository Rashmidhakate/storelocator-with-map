<?php 

use Magento\Framework\App\Bootstrap;
require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$objectManager = $bootstrap->getObjectManager();

$fileUrl = 'Unique_image_with_product_new_1.csv'; 


// $state = $objectManager->get('Magento\Framework\App\State');
// $state->setAreaCode('frontend');

$header = NULL;
$data = array();
	if (($handle = @fopen($fileUrl, 'r')) !== FALSE)
	{
		while (($row = @fgetcsv($handle, 100000, ",")) !== FALSE)
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

			$dot_sku = $csvdata['missing_sku'];
			//$image = $csvdata['Large_Image14'];
			$array = explode(',',$csvdata['Match_Sku']);
			$image1 = array_filter($array);  
			// $result = str_replace('-', '', $dot_sku);
			// print_r($image1);
			// exit;
			try{
			$productModel = $objectManager->create('Magento\Catalog\Model\ProductFactory');
			$productBySku = $productModel->create()->getIdBySku($dot_sku);
			if($productBySku) {
				echo $productBySku." exist \n";
				// echo sizeof($image1);
				// exit;
				if(sizeof($image1) == 1) {
					// print_r($image1);
					// exit;
					$product = $productModel->create()->load($productBySku);
					$productRepository = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');
					$existingMediaGalleryEntries = $product->getMediaGalleryEntries();
					
					foreach ($existingMediaGalleryEntries as $key => $entry) {
						unset($existingMediaGalleryEntries[$key]);
					}
					$product->setMediaGalleryEntries($existingMediaGalleryEntries);
					$productRepository->save($product);
					$filespath = $objectManager->get('\Magento\Framework\Filesystem');
					$directoryList = $objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList');
					$file = $objectManager->get('\Magento\Framework\Filesystem\Io\File');
					//$prdbasepath = $filespath->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();

					$tmpDir = $directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp';

					$file->checkAndCreateFolder($tmpDir);
					$image_directory = $tmpDir ."/". baseName($image1[0]);
					$result = $file->read($image1[0], $image_directory);
					if($result){
						if (file_exists($image_directory) && getimagesize($image_directory)) {
							$product->addImageToMediaGallery($image_directory, null, false, false);
							$product->save();
						}
					}
					$existingMediaGalleryEntries = $product->getMediaGalleryEntries();

					foreach ($existingMediaGalleryEntries as $key => $entry) {
						/* from csv uploaded first image set as base, thumbnail,small image*/
						if($key == 0){
							$entry->setTypes(['thumbnail', 'image', 'small_image']);
						}
					}
					$product->setMediaGalleryEntries($existingMediaGalleryEntries);
					$productRepository->save($product);
				}
				if(sizeof($image1) > 1) {
					$product = $productModel->create()->load($productBySku);
					$productRepository = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');
					$existingMediaGalleryEntries = $product->getMediaGalleryEntries();
					
					foreach ($existingMediaGalleryEntries as $key => $entry) {
						unset($existingMediaGalleryEntries[$key]);
					}
					$product->setMediaGalleryEntries($existingMediaGalleryEntries);
					$productRepository->save($product);
					$filespath = $objectManager->get('\Magento\Framework\Filesystem');
					$directoryList = $objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList');
					$file = $objectManager->get('\Magento\Framework\Filesystem\Io\File');
					//$prdbasepath = $filespath->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();

					$tmpDir = $directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp';

					$file->checkAndCreateFolder($tmpDir);
					for ( $i=0; $i < sizeof($image1); $i++ ) {
						$image_directory = $tmpDir ."/". baseName(trim($image1[$i]));
						$result = $file->read(trim($image1[$i]), $image_directory);
						if($result){
							if (file_exists($image_directory) && getimagesize($image_directory)) {
								$product->addImageToMediaGallery($image_directory, null, false, false);
								$product->save();
							}
						}
					}
					$existingMediaGalleryEntries = $product->getMediaGalleryEntries();

					foreach ($existingMediaGalleryEntries as $key => $entry) {
						/* from csv uploaded first image set as base, thumbnail,small image*/
						if($key == 0){
							$entry->setTypes(['thumbnail', 'image', 'small_image']);
						}
					}
					$product->setMediaGalleryEntries($existingMediaGalleryEntries);
					$productRepository->save($product);

				}
				$productExistCount++;

			}else{
				echo $productBySku." not exist \n";

			}
			echo $productExistCount." successfully created \n";
		} catch (\Magento\Framework\Exception\InputException $e) {
			echo $e->getMessage() . "\n";
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			echo $e->getMessage() . "\n";
		}
			//exit;
		}
		
	}
echo "hello";
?>