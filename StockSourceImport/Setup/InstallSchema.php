<?php
namespace Brainvire\StockSourceImport\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
 
        $installer->startSetup();
 
        $table = $installer->getConnection()
            ->newTable($installer->getTable('stock_source_import_data'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
              ->addColumn(
                'source_code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Source Code'
            )
              ->addColumn(
                'user',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'User'
            )
              ->addColumn(
                'success_record',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Success Record'
            )
              ->addColumn(
                'failed_record',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Failed Record'
            )
              ->addColumn(
                'csv',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'csv'
            )
           ->addColumn(
                'created_at',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Created At'
            )
            ->setComment('Stock Source Import Data');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}