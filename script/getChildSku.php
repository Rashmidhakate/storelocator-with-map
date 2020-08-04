<?php

use Magento\Framework\App\Bootstrap;
require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');
$objectManager = $bootstrap->getObjectManager();

$collection = $objectManager->create('Magento\Catalog\Model\Product')->getCollection();
$collection->addAttributeToSelect('*');
$collection->addAttributeToFilter('manufacturer', ['eq' => 72]);
$collection->addAttributeToFilter('metals', ['eq' => 695]);
echo $collection->count();
echo "\n";
// exit;

$heading = [
    __('sku'),
    __('price'),
    __('special_price'),
    __('type'),

];
echo $outputFile = "Larsonjewelers_Manufacture_Metals" . date('Ymd_His') . "_Associated_Simple.csv";
$handle = fopen($outputFile, 'w');
fputcsv($handle, $heading);
echo "\n Start Excution \n";

/**/
$ExcludeSku = array(
    'W6126-DPB',
    'F860-DPTC',
    'W321-FPB',
    'W635-BCWT',
    'W241-RSC',
    'W759-TMOP',
    'W589-FBBT',
    'W1967-DPGT',
    'W5969-GPBT',
    'W2048-FBBE',
    'W486-DBBT',
    'W485-BBPB',
    'W335-WCFT',
    'W278-LE1',
    'W339-DBT',
    'F1299-FBBT',
    'W636-DHF',
    'W858-FPB',
    'W1971-WBST',
    'W1266-WBST',
    'W333-FPB',
    'W589-FBBTB',
    'W486-DBBTM',
    'W243-EWFP',
    'W6126-DPBD',
    'W333-FPBB',
);

/**/

foreach ($collection as $_product) {
    echo "Here are Parent Product Name : " . $_product->getSku();
    echo "\n";
    if (!in_array($_product->getSku(), $ExcludeSku)) {
        if ($_product->getTypeId() == "configurable") {
            $_children = $_product->getTypeInstance()->getUsedProducts($_product);

            foreach ($_children as $child) {
                echo "\t Sku : " . $child->getSku();
                echo "\t Price : " . $price = $child->getPrice();
                echo "\t SpecialPrice : " . $special_price = $child->getSpecialPrice();
// break;
                echo "\n";
                $productData = [
                    $child->getSku(),
                    $price,
                    $special_price,
                ];
                fputcsv($handle, $productData);
            }
        } else {
            echo "Sku : " . $_product->getSku();
            echo "Price : " . $_productprice = $_product->getPrice();
            echo "SpecialPrice : " . $_productspecial_price = $_product->getSpecialPrice();
            echo "\n";
            $productDataMain = [
                $_product->getSku(),
                $_productprice,
                $_productspecial_price,
            ];
            fputcsv($handle, $productDataMain);
        }
    }
}

echo "\nEnd Excution \n";
exit();
