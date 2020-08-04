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
$fileUrl = 'increase_product_price_by_30.csv';
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
                        $productModelChild->setStoreId(1);
                        $productModelChild->save();
                        echo "save" . "\n";
                        echo $productModelChild->getSku()."\n";
                        //  exit;
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
