<?php 

use Magento\Framework\App\Bootstrap;
require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$objectManager = $bootstrap->getObjectManager();
$resource = $obj->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();

//$Url = "select entity_id,sku from catalog_product_entity where type_id = 'configurable' And NOT EXISTS (select entity_id from   url_rewrite where  catalog_product_entity.entity_id = url_rewrite.entity_id)";
$Url = "select entity_id,sku from catalog_product_entity where type_id = 'simple' And NOT EXISTS (select entity_id from   url_rewrite where  catalog_product_entity.entity_id = url_rewrite.entity_id)";
$result = $connection->fetchAll($Url);
// echo "<pre>";
// print_r($result->getData());


 $heading = [
    __('entity_id'),
    __('sku')
    ];
    $outputFile = "larsonjewelers_not_exist_url_rewrite". date('Ymd_His').".csv";
    $handle = fopen($outputFile, 'w');
    fputcsv($handle, $heading);
    foreach ($result as $product) {
      $count++;
      echo $count."\n";
      // echo $product['entity_id']."\n";
      // echo $product['sku']."\n";
     $row = [
             $product['entity_id'],
             $product['sku'],        
         ];
         fputcsv($handle, $row);
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
?>