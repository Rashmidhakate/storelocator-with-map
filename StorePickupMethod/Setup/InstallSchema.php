<?php
namespace Brainvire\StorePickupMethod\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();   
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'store_address',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true  ,
                'length' => 255,
                'comment' => 'Store Addess'
            ]
        );
        
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'store_address',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true  ,
                'length' => 255,
                'comment' => 'Store Addess'
            ]
        );
        
        $setup->endSetup();
  }
}