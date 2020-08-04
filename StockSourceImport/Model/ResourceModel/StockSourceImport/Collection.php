<?php
namespace Brainvire\StockSourceImport\Model\ResourceModel\StockSourceImport;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Brainvire\StockSourceImport\Model\StockSourceImport', 'Brainvire\StockSourceImport\Model\ResourceModel\StockSourceImport');
	}
	protected function _initSelect()
	{
		parent::_initSelect();
		$this->getSelect()->joinLeft(
		    ['selection' => $this->getTable('inventory_source')],
		    'main_table.source_code = selection.source_code',
		    ['*']
		);
	}

}

