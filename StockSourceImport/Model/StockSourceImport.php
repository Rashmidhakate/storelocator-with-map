<?php
namespace Brainvire\StockSourceImport\Model;

class StockSourceImport extends \Magento\Framework\Model\AbstractModel 
{
	protected function _construct()
	{
		$this->_init('Brainvire\StockSourceImport\Model\ResourceModel\StockSourceImport');
	}
}