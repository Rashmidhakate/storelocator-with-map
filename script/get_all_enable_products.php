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
$count = 0;
$productCollectionFactory = $obj->create('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
$collection = $productCollectionFactory->create();
$collection->addAttributeToSelect('*');
$collection->addFieldToFilter('status', array('eq' => 1));
$collection->addFieldToFilter('type_id', array('eq' => "configurable"));
//$collection->joinField('stock_item', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', 'is_in_stock=0' ,'qty!=0');
//$collection->load();
$prodcutCollection = $collection->getData();
// print_r($prodcutCollection);
// exit;
 $heading = [
    __('entity_id'),
    __('type_id'),
    __('sku'),
    __('status'),
    __('website_id'),
    ];
    $outputFile = "larsonjewelers_configurable_product". date('Ymd_His').".csv";
    $handle = fopen($outputFile, 'w');
    fputcsv($handle, $heading);
foreach($prodcutCollection as $data){
    $count++;
    $productCollectionFactory = $obj->create('\Magento\Catalog\Model\ProductFactory');
    $product = $productCollectionFactory->create();
    $product->load($data['entity_id']);

    // print_r($product->getData());
    // exit;
    echo $product->getId()."\n";
    echo $product->getQty()."\n";
    echo $product->getPrice()."\n";
    echo $product->getTypeId()."\n";
    echo implode(",",$product->getWebsiteIds());
    //print_r($product->getWebsiteIds())."\n";

    // $row = [
    //     $data['entity_id'],
    //     $data['type_id'],
    //     $data['sku'],
    //     $data['status'],
    // ];

    $row = [
        $product->getId(),
        $product->getTypeId(),
        $product->getSku(),
        $product->getStatus(),
        implode(",",$product->getWebsiteIds()),
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