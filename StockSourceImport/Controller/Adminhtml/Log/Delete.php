<?php

namespace Brainvire\StockSourceImport\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Framework\Filesystem\Driver\File;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Prince\Productattach\Model\Productattach
     */
    private $attachModel;

    /**
     * @param \Magento\Backend\App\Action $context
     * @param \Prince\Productattach\Model\Productattach $attachModel
     */
    public function __construct(
        Action\Context $context,
        \Brainvire\StockSourceImport\Model\StockSourceImport $stockSourceImport,
        \Magento\Framework\Filesystem $fileSystem,
        File $file
    ) {
        $this->stockSourceImport = $stockSourceImport;
        $this->fileSystem = $fileSystem;
        $this->_file = $file;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Brainvire_StockSourceImport::delete');
    }

    /**
     * Delete action
     *
     * @return void
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $model = $this->stockSourceImport;
                $model->load($id);
                $filename = $model->getCsv();
                $mediaDirectory = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                $mediaRootDir = $mediaDirectory->getAbsolutePath();
                $directoryPath = $mediaRootDir.'stocksourceimport/upload/' . $filename;
                if ($this->_file->isExists($directoryPath))  {
                    $this->_file->deleteFile($directoryPath);
                }
                $model->delete();
                $this->messageManager->addSuccess(__('The data has been deleted.'));
                return $resultRedirect->setPath('sourceimportlog/log/index');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('sourceimportlog/log/index');
            }
        }
        $this->messageManager->addError(__('We can\'t find a data to delete.'));
        return $resultRedirect->setPath('sourceimportlog/log/index');
    }
}
