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
$optionLabelFactory=$_objectManager->create('\Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory');
$optionFactory=$_objectManager->create('\Magento\Eav\Api\Data\AttributeOptionInterfaceFactory');
$attributeOptionManagement=$_objectManager->create('\Magento\Eav\Api\AttributeOptionManagementInterface');
$attributeRepository=$_objectManager->create('\Magento\Catalog\Api\ProductAttributeRepositoryInterface');
$productFactory=$_objectManager->create('\Magento\Catalog\Model\ProductFactory');
$count=0;
$fp = fopen("product_with_range_all_new_width-1.csv","r");
if (empty($fp) === false) {
    while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) {
        
        if($count<=0)
        {
        	$count++;
        	continue;
        }
        
       
       

        $productId=$data[0];
        $product_width_new_label=$data[3];
        $attribute_id=225;
        $attributeCode="width_new";
        $attributeObj=$attributeRepository->get($attributeCode);

        if (strlen($product_width_new_label) >= 1) {
            $_product = $productFactory->create();
	        $isAttributeExist = $_product->getResource()->getAttribute($attributeCode);
	      
	        if ($isAttributeExist && $isAttributeExist->usesSource()) {
	            $optionId = $isAttributeExist->getSource()->getOptionId($product_width_new_label);
	        }
	       
             if (!$optionId) {
	           
	            $optionLabel = $optionLabelFactory->create();
	            $optionLabel->setStoreId(0);
	            $optionLabel->setLabel($product_width_new_label);

	            $option = $optionFactory->create();
	            $option->setLabel($product_width_new_label);
	            $option->setStoreLabels([$optionLabel]);
	            $option->setSortOrder(0);
	            $option->setIsDefault(false);

	            $attributeOptionManagement->add(
	                \Magento\Catalog\Model\Product::ENTITY,
	                $attribute_id,
	                $option
	            );

	           
	            $_product = $productFactory->create();
		        $isAttributeExist = $_product->getResource()->getAttribute($attributeCode);
		      
		        if ($isAttributeExist && $isAttributeExist->usesSource()) {
		            $optionId = $isAttributeExist->getSource()->getOptionId($product_width_new_label);
		        }
	        }
	        $product = $_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
 			var_dump($productId);
			 var_dump($optionId);
			// $product->setWidthNew($optionId);
			// $product->save();
			 $product->addAttributeUpdate($attributeCode, $optionId, 0);
	       
        }

        
    }
    fclose($fp);
  }


?>