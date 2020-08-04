<?php

use Magento\Framework\App\Bootstrap;

require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$_objectManager = $bootstrap->getObjectManager();

// Set the state (not sure if this is neccessary)
$state = $_objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$registry = $_objectManager->get('Magento\Framework\Registry');
$registry->register('isSecureArea', true);

//Store id of exported products, This is useful when we have multiple stores. 
$store_id = 0;

$fp = fopen("product_with_range_all.csv","w+");
$collection = $_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory')->create()->addStoreFilter($store_id)->addAttributeToSelect(array('entity_id','sku','width'));
//$collection->addAttributeToFilter('width',array('neq'=>''));
var_dump($collection->getSize());

echo "outer loop";
foreach ($collection as $product)
{
  //  echo "inner loop";
    $data = array();
    $data[] = $product->getId();
    $data[] = $product->getWidth();
    $data[] = $product->getResource()->getAttribute('width')->getFrontend()->getValue($product);
   // var_dump($product->getWidth());

  //  exit;
   
    fputcsv($fp, $data);    
}
echo "done";
fclose($fp);
?>