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
$fileUrl = 'mgt_server_matching_set.csv';
$product = $obj->create('\Magento\Catalog\Model\Product');
$productRepository = $obj->get('Magento\Catalog\Api\ProductRepositoryInterface');
$optionFactory = $obj->get('\Magento\Catalog\Model\Product\Option');
$header = NULL;
$data = array();
$associatedProductIds = array();
$sizeProductOptions = array();
$widthProductOptions = array();
$totalrecords = 0;
$productExistCount = 0;
$productNotExistCount = 0;
if (($handle = @fopen($fileUrl, 'r')) !== FALSE) {
    while (($row = @fgetcsv($handle, 10000, ",")) !== FALSE) {
        if (!$header) {
            $header = $row;
        } else {
            $data[] = array_combine($header, $row);
        }

    }
    $sourceItems = [];
    $categoryArray = [];
    foreach ($data as $csvdata) {
        $totalrecords++;
        echo $totalrecords . "\n";
        $sku = $csvdata['sku'];
        $price = $csvdata['price'];
        $specialPrice = $csvdata['special_price'];
        try {
            $productModel = $obj->create('Magento\Catalog\Model\Product');
            $productBySku = $productModel->getIdBySku($sku);
            if ($productBySku) {
                $productExistCount++;
                $productModel->load($productBySku);

                echo $productModel->getId() . "\n";
                $associateIds = array();
                // $_children = $obj->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable');
                // foreach ($_children->getChildrenIds($productModel->getId()) as $child) {
                //     foreach ($child as $value) {
                        $productModelChild = $obj->get('Magento\Catalog\Model\ProductFactory')->create();
                        $productModelChild->load($productBySku);
                        $productModelChild->setPrice($price);
                        $productModelChild->setSpecialPrice($specialPrice);
                        $productModelChild->setAttributeSetId(9);
                        //$productModelChild->setHasOptions(1);
                        $productModelChild->setStatus(1);
                        $productModelChild->setVisibility(4);
                        $productModelChild->setTypeId('simple');
                        $productModelChild->setStockData(
                            array(
                                'use_config_manage_stock' => 0,
                                'manage_stock' => 1,
                                'is_in_stock' => 1,
                                'qty' => 1000
                            )
                        );
                        $productModelChild->save();
                        //$productModelChild->getResource()->save($productModelChild);
                        //$productModelChild->save();
                        $resource = $obj->get('Magento\Framework\App\ResourceConnection');
                        $connection = $resource->getConnection();
                        $salesInvoiceChangeDate = "UPDATE `catalog_product_entity` SET `has_options` = 1,`required_options` = 1 WHERE `sku` = '" . $productModelChild->getSku() . "'";
                        $connection->query($salesInvoiceChangeDate);
                        echo "save" . "\n";
                        echo $productModelChild->getSku()."\n";
                        exit;
                //     }
                // }

              
            }
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
