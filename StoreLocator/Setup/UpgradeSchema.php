<?php

namespace Brainvire\StoreLocator\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), "2.0.1", "<")) {
        //Your upgrade script
        }
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
          $installer->getConnection()
          ->addColumn(
                $installer->getTable('brainvire_storelocator_store'),
                'store_ids',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'Store Id'
                ]
            );
            $installer->getConnection()
            ->addColumn(
                $installer->getTable('brainvire_storelocator_store'),
                'hours',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Working From'
                ]
            );
            $installer->getConnection()
            ->addColumn(
                $installer->getTable('brainvire_storelocator_store'),
                'working_to',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Working To'
                ]
            );
            $installer->getConnection()
            ->addColumn(
                $installer->getTable('brainvire_storelocator_store'),
                'location',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Location'
                ]
            );
           

        }
        $installer->endSetup();
    }
}