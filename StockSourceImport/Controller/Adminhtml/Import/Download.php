<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Brainvire\StockSourceImport\Controller\Adminhtml\Import;

use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \Magento\Backend\App\Action
{

    const IMPORT_HISTORY_DIR = 'stocksourceimport/';
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct(
            $context
        );
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->pubDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * Download backup action
     *
     * @return void|\Magento\Backend\App\Action
     */
    public function execute()
    {
        $fileName = 'product_stock_source.csv';
        if (!$this->importFileExists($fileName)) {
             /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('stocksourceimport/import/index');
            return $resultRedirect;
        }
        $this->fileFactory->create(
            $fileName,
            null,
            DirectoryList::VAR_DIR,
            'application/octet-stream',
            $this->getReportSize($fileName)
        );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($this->getReportOutput($fileName));
        return $resultRaw;
    }

     /**
     * Checks imported file exists.
     *
     * @param string $filename
     * @return bool
     */
    public function importFileExists($filename)
    {
        return $this->pubDirectory->isFile($this->getFilePath($filename));
    }

        /**
     * Get file path.
     *
     * @param string $filename
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getFilePath($filename)
    {
        if (preg_match('/\.\.(\\\|\/)/', $filename)) {
            throw new \InvalidArgumentException('Filename has not permitted symbols in it');
        }

        return $this->pubDirectory->getRelativePath('stocksourceimport/' . $filename);
    }

    /**
     * Retrieve report file size
     *
     * @param string $filename
     * @return int|mixed
     */
    public function getReportSize($filename)
    {
        return $this->pubDirectory->stat($this->getFilePath($filename))['size'];
    }

    /**
     * Get report file output
     *
     * @param string $filename
     * @return string
     */
    public function getReportOutput($filename)
    {
        return $this->pubDirectory->readFile($this->getFilePath($filename));
    }
    
}
