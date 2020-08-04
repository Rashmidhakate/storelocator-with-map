<?php

use Magento\Framework\App\Bootstrap;

require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$obj->get('Magento\Framework\Registry')->register('isSecureArea', true);


$resource = $obj->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();
$tableName = $resource->getTableName('eav_attribute'); 

$sql = "UPDATE `eav_attribute` SET `backend_model`='Magento".DS."Eav".DS."Model".DS."Entity".DS."Attribute".DS."Backend".DS."ArrayBackend',`backend_type`='varchar',`frontend_input`='multiselect', `source_model`=NULL WHERE `attribute_id`=139 LIMIT 1";
//$sql = "UPDATE `eav_attribute` SET `backend_model`=NULL,`backend_type`='int',`frontend_input`='select', `source_model`=NULL WHERE `attribute_id`=162 LIMIT 1";
// echo $sql;
// exit;

$connection->query($sql);
echo "hello" ;
exit;
?>