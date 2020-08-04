<?php 

use Magento\Framework\App\Bootstrap;
require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$objectManager = $bootstrap->getObjectManager();

$fileUrl = '404_error_product_url.csv'; 


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
      __('sku'),
      __('name'),
      __('url_key')
      ];
      $outputFile = "larsonjewelers_disable_not_visible_individual_product". date('Ymd_His').".csv";
      $handle = fopen($outputFile, 'w');
      fputcsv($handle, $heading);
    foreach ($data as $csvdata) {
      //$sourceItems[] = trim($csvdata['url']);
      $lines = array();
      // print_r($sourceItems);
      // exit;

      $productCollectionFactory = $obj->create('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
      $collection = $productCollectionFactory->create();
      $collection->addAttributeToSelect('*');
      $collection->addFieldToFilter('url_key', array('eq' => trim($csvdata['url'])));
      $collection->addFieldToFilter('visibility', array('eq' => 1));
      $collection->addFieldToFilter('status', array('eq' => 0));

      // echo $collection->getSelect()->__toString();
      // exit;
      // $collection->load();
      // $prodcutCollection = $collection->getData();
      if($collection->getData()){
        foreach ($collection as $product) {

          $count++;
          echo $count."\n";
          $lines = [
            $product->getEntityId(),
            $product->getSku(),
            $product->getName(),
            $product->getUrlKey(),          
          ];
      
           // echo "id".$product->getEntityId()."\n";
           //    echo "sku".$product->getSku()."\n";
           //       echo "url".$product->getUrlKey()."\n"; 
        }
        //echo "<pre>";
        // // // echo $prodcutCollection[0]['sku'];
        if($lines){
          fputcsv($handle, $lines);
        }
      }
    // exit;
      // if($prodcutCollection){
    
      

      //foreach ($collection as $product) {
     

      // //}
    //fputcsv($handle, $lines);
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

    }
  
echo "hello";
?>