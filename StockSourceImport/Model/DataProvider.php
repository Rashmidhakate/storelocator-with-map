<?php
namespace Brainvire\StockSourceImport\Model;

use Brainvire\StockSourceImport\Model\ResourceModel\StockSourceImport\CollectionFactory;
 
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
	/**
	* @param string $name
	* @param string $primaryFieldName
	* @param string $requestFieldName
	* @param CollectionFactory $sourceCollectionFactory
	* @param array $meta
	* @param array $data
	*/
	public function __construct(
	$name,
	$primaryFieldName,
	$requestFieldName,
	CollectionFactory $sourceCollectionFactory,
	array $meta = [],
	array $data = []
	) {
		$this->collection = $sourceCollectionFactory->create();
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
	}

	/**
	* Get data
	*
	* @return array
	*/
	public function getData()
	{
		return [];
	}
}