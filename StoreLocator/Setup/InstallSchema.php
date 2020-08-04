<?php
namespace Brainvire\StoreLocator\Setup;

use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $categoryTable = $installer->getTable('brainvire_storelocator_category');
        $storeTable = $installer->getTable('brainvire_storelocator_store');

        $table = $installer->getConnection()->newTable(
            $categoryTable
        )->addColumn(
            'category_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false]
        )->addColumn(
            'icon',
            Table::TYPE_TEXT,
            512,
            ['nullable' => true, 'default' => null]
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1']
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_ad',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Updated At'
        )->addIndex(
            $installer->getIdxName(
                $categoryTable,
                ['category_id']
            ),
            ['category_id']
        );

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $storeTable
        )->addColumn(
            'store_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addColumn(
            'category_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'deafult' => null],
            'Category ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => null]
        )->addColumn(
            'address',
            Table::TYPE_TEXT,
            312,
            ['nullable' => true, 'default' => null]
        )->addColumn(
            'postcode',
            Table::TYPE_TEXT,
            100,
            ['nullable' => true, 'default' => null]
        )->addColumn(
            'city',
            Table::TYPE_TEXT,
            100,
            ['nullable' => true, 'default' => null]
        )->addColumn(
            'country',
            Table::TYPE_TEXT,
            2,
            ['nullable' => true, 'default' => null]
        )->addColumn(
            'phone',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => null]
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => null]
        )->addColumn(
            'fax',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => null]
        )->addColumn(
            'website',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => null]
        )->addColumn(
            'lat',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null]
        )->addColumn(
            'lng',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null]
        )->addColumn(
            'zoom',
            Table::TYPE_TEXT,
            '5',
            ['nullable' => true, 'default' => null]
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1']
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_ad',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Updated At'
        )->addIndex(
            $installer->getIdxName(
                $storeTable,
                ['store_id']
            ),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName($storeTable, 'category_id', $categoryTable, 'category_id'),
            'category_id',
            $categoryTable,
            'category_id',
            Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
