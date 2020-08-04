<?php
namespace Brainvire\StockSourceImport\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\Filesystem\Driver\File;

class Download extends Template
{

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        File $file,
        \Magento\Framework\Filesystem $fileSystem,
        array $data = []
    ) {
    	$this->_file = $file;
    	$this->fileSystem = $fileSystem;
        parent::__construct($context, $data);
    }

    public function getFilePath()
    {
    	return $this->getUrl("stocksourceimport/import/download");
    }

}