<?php
namespace Brainvire\StockSourceImport\Controller\Adminhtml\Import;
 
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\InventoryCatalog\Model\GetDefaultSourceItemBySku;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
 
class Save extends \Magento\Backend\App\Action
{
	protected $authSession;

	public function __construct(
		Action\Context $context,
		\Brainvire\StockSourceImport\Model\StockSourceImport $stockSourceImport,
		\Magento\Backend\Model\Auth\Session $authSession,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		GetDefaultSourceItemBySku $getDefaultSourceItemBySku,
		SourceItemsSaveInterface $sourceItemsSave,
		SourceItemInterfaceFactory $sourceItemFactory,
		\Magento\Catalog\Model\Product $productCollection
    ) {
        $this->stockSourceImport = $stockSourceImport;
		$this->authSession = $authSession;
		$this->_date = $date;
		$this->getDefaultSourceItemBySku = $getDefaultSourceItemBySku;
		$this->sourceItemsSave = $sourceItemsSave;
		$this->sourceItemFactory = $sourceItemFactory;
		$this->productCollection = $productCollection;
        parent::__construct($context);
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
    	$data = $this->getRequest()->getPostValue();
    	$postSourceCode = $data["source_code"];
    	$fileName = $data["csv"][0]["name"];
    	$fileUrl = $data["csv"][0]["url"];
    	$user = $this->authSession->getUser();
    	$userName = $user->getUserName();
    	$header = NULL;
		$data = array();
			if (($handle = @fopen($fileUrl, 'r')) !== FALSE)
			{
				while (($row = @fgetcsv($handle, 1000, ",")) !== FALSE)
				{
					if(!$header)
						$header = $row;
					else
						$data[] = array_combine($header, $row);
				}
				$productExistCount = 0;
				$productNotExistCount = 0;
				$sourceItems = [];
				foreach ($data as $csvdata) {
					$sourceCode = $csvdata['source_code'];
					$sku = $csvdata['sku'];
					$stockStatus = $csvdata['status'];
					$stockQty = $csvdata['quantity'];
					$product = $this->productCollection;
					$productBySku = $product->getIdBySku($sku);
					if($productBySku) {
						if($postSourceCode == $sourceCode){
							$sourceItem = $this->sourceItemFactory->create();
							$sourceItem->setSku((string)$sku);
							$sourceItem->setSourceCode($sourceCode);
							$sourceItem->setQuantity($stockQty);
							$sourceItem->setStatus($stockStatus);
							$sourceItems[] = $sourceItem;
							if (count($sourceItems) > 0) {
								 /** SourceItemInterface[] $sourceItems */
								$this->sourceItemsSave->execute($sourceItems);
							}
							$productExistCount++;
						}else{
							$productNotExistCount++;
						}
					}else{
						$this->messageManager->addErrorMessage("'".$sku."'"."does not exist. ");
					}
				}
				$this->messageManager->addSuccessMessage("Aryamond Data Successfully inserted.");
				$stockImportModel = $this->stockSourceImport;
				$stockImportModel->setSourceCode($postSourceCode);
				$stockImportModel->setCsv($fileName);
				$stockImportModel->setUser($userName);
				$stockImportModel->setSuccessRecord($productExistCount);
				$stockImportModel->setFailedRecord($productNotExistCount);
				$stockImportModel->setCreatedAt($this->_date->gmtDate());
				$stockImportModel->save();
			}
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$resultRedirect->setUrl($this->_redirect->getRefererUrl());
		return $resultRedirect;
    }
}