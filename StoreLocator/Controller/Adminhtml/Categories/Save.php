<?php
namespace Brainvire\StoreLocator\Controller\Adminhtml\Categories;

use \Brainvire\StoreLocator\Controller\Adminhtml\Categories;
use \Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\View\Result\PageFactory;
use \Brainvire\StoreLocator\Api\CategoryRepositoryInterface;
use \Magento\PageCache\Model\Config;
use \Magento\Framework\App\Cache\TypeListInterface;
use \Magento\MediaStorage\Model\File\UploaderFactory;
use \Brainvire\StoreLocator\Model\Category\Icon;
use \Brainvire\StoreLocator\Api\Data\CategoryInterfaceFactory;

class Save extends Categories
{
    /**
     * @var \Magento\PageCache\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    private $typeList;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var Icon
     */
    private $iconModel;

    /**
     * Save constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterfaceFactory $categoryFactory
     * @param Config $config
     * @param TypeListInterface $typeList
     * @param UploaderFactory $uploaderFactory
     * @param Icon $iconModel
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory,
        Config $config,
        TypeListInterface $typeList,
        UploaderFactory $uploaderFactory,
        Icon $iconModel
    ) {
        $this->config = $config;
        $this->typeList = $typeList;
        $this->uploaderFactory = $uploaderFactory;
        $this->iconModel = $iconModel;
        parent::__construct($context, $resultPageFactory, $categoryRepository, $categoryFactory);
    }

    /**
     * Save store
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($this->config->isEnabled()) {
            $this->typeList->invalidate('full_page');
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $category = $this->categoryFactory->create();

            $category->setData($data);

            $this->_eventManager->dispatch(
                'storelocator_category_prepare_save',
                ['category' => $category, 'request' => $this->getRequest()]
            );

            try {
                $iconName = $this->uploadFileAndGetName('icon', $this->iconModel->getBaseDir(), $data);
                $category->setIcon($iconName);
                $this->categoryRepository->save($category);

                $this->messageManager->addSuccessMessage(__('The category has been saved.'));
                $this->_getSession()->setFormData(false);

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['category_id' => $category->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the category.') . $e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['category_id' => $this->getRequest()->getParam('category_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * @param $input
     * @param $destinationFolder
     * @param $data
     * @return null|string
     * @throws LocalizedException
     */
    private function uploadFileAndGetName($input, $destinationFolder, $data)
    {
        try {
            if (isset($data[$input]['delete'])) {
                return null;
            } else {
                $uploader = $this->uploaderFactory->create(['fileId' => $input]);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $result = $uploader->save($destinationFolder);
                return $result['file'];
            }
        } catch (\Exception $e) {
            if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                throw new LocalizedException(__($e->getMessage()));
            } else {
                if (isset($data[$input]['value'])) {
                    return $data[$input]['value'];
                }
            }
        }
        return '';
    }
}
