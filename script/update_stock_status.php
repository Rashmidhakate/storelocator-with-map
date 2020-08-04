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

$fileUrl = 'outof_stock_product.csv';
$header = null;
$data = array();
$count = 0;
if (($handle = @fopen($fileUrl, 'r')) !== false) {
	while (($row = @fgetcsv($handle, 1000, ",")) !== false) {
	    if (!$header) {
	        $header = $row;
	    } else {
	        $data[] = array_combine($header, $row);
	    }

	}
	 foreach ($data as $csvdata) {
	 	$count++;
		$productCollectionFactory = $obj->create('\Magento\Catalog\Model\ProductFactory');
		$product = $productCollectionFactory->create();
		$product->load($csvdata['entity_id']);
		$product->setStockData(
			array(
			'use_config_manage_stock' => 0,
			'manage_stock' => 1,
			'is_in_stock' => 1,
			'qty' => $csvdata['stock_item']
			)
		);

		$product->save(); 
	 	//print_r($csvdata);
	 	echo $count."\n";
	 }
}

?>