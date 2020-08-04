<?php 

use Magento\Framework\App\Bootstrap;
require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$objectManager = $bootstrap->getObjectManager();

$fileUrl = 'products_in_both_site.csv'; 


// $state = $objectManager->get('Magento\Framework\App\State');
// $state->setAreaCode('frontend');

$header = NULL;
 $count = 0 ;
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
      $sourceItems = [];
      $heading = [
    __('entity_id'),
    __('type_id'),
    __('sku'),
    __('status'),
    __('website_id'),
    ];
    $outputFile = "simple_larsonjewelers_configurable_simple_product". date('Ymd_His').".csv";
    $handle = fopen($outputFile, 'w');
    fputcsv($handle, $heading);
    foreach ($data as $csvdata) {
       $row = [
          $csvdata['entity_id'],
          $csvdata['type_id'],
          $csvdata['sku'],
          $csvdata['status'],
          $csvdata['website_id'],
        ];
        fputcsv($handle, $row);
      $lines = array();
      $count++;
      $productCollectionFactory = $obj->create('\Magento\Catalog\Model\ProductFactory');
      $product = $productCollectionFactory->create();
      $product->load($csvdata['entity_id']);
      $_children = $product->getTypeInstance()->getUsedProducts($product);
      foreach ($_children as $child){
        $row = [
          $child->getId(),
          $child->getTypeId(),
          $child->getSku(),
          $child->getStatus(),
          implode(",",$child->getWebsiteIds()),
        ];
        fputcsv($handle, $row);
      }
    }
     
    if (file_exists($outputFile)) {
    $fileFactory = $obj->get('\Magento\Framework\App\Response\Http\FileFactory');
    $fileFactory->create(
            $outputFile,
            null, //content here. it can be null and set later 
            '',
            'application/octet-stream', //content type here
           ''
        );
     }
     //exit;

    }
  
echo "hello";
?>