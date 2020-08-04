<?php
namespace Brainvire\StockSourceImport\Controller\Adminhtml\Log;
 
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Brainvire\StockSourceImport\Model\ResourceModel\StockSourceImport\CollectionFactory;
use Magento\Framework\Filesystem\Driver\File;
 
class MassDelete extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Brainvire_StockSourceImport::stocksourceimport_delete';
    /**
     * @var Filter
     */
    protected $filter;
 
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
 
 
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context, 
        Filter $filter, 
        CollectionFactory $collectionFactory,
         \Magento\Framework\Filesystem $fileSystem,
        File $file
        )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->fileSystem = $fileSystem;
        $this->_file = $file;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $filename = $item->getCsv();
            $mediaDirectory = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $mediaRootDir = $mediaDirectory->getAbsolutePath();
            $directoryPath = $mediaRootDir.'stocksourceimport/upload/' . $filename;
            if ($this->_file->isExists($directoryPath))  {
                $this->_file->deleteFile($directoryPath);
            }
            $item->delete();
        }
 
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));
 
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
