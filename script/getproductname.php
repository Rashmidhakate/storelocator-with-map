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

$fileUrl = 'simple_error_url.csv';

// $state = $objectManager->get('Magento\Framework\App\State');
// $state->setAreaCode('frontend');

$header = null;
$data = array();
if (($handle = @fopen($fileUrl, 'r')) !== false) {
    while (($row = @fgetcsv($handle, 1000, ",")) !== false) {
        if (!$header) {
            $header = $row;
        } else {
            $data[] = array_combine($header, $row);
        }

    }
    $sourceItems = [];
    $heading = [
        __('entity_id'),
        __('sku'),
        __('url_key'),
        __('name'),
    ];
    $outputFile = "larsonjewelers_simple_product_name" . date('Ymd_His') . ".csv";
    $handle = fopen($outputFile, 'w');
    fputcsv($handle, $heading);
    $count = 0;
    foreach ($data as $csvdata) {
        //$sourceItems[] = trim($csvdata['entity_id']);

        // print_r($sourceItems);
        // exit;

        $productCollectionFactory = $obj->create('\Magento\Catalog\Model\ProductFactory');
        $collection = $productCollectionFactory->create();
        $collection->load($csvdata['entity_id']);
        // $collection->addAttributeToSelect('*');
        // $collection->addFieldToFilter('entity_id', array('in' => $sourceItems));
        // $collection->addFieldToFilter('type_id', array('eq' => 'configurable'));
        // foreach ($collection as $product) {
        //    echo "id".$product->getEntityId()."\n";
        //       echo "sku".$product->getSku()."\n";
        //          echo "url".$product->getUrlKey()."\n";
        // }
        // echo "<pre>";
        // print_r($collection->getId());
        //  exit;
        $count++;
        echo $count . "\n";
        $row = [
            $collection->getId(),
            $collection->getSku(),
            $collection->getUrlKey(),
            $collection->getName(),
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

}

echo "hello";
