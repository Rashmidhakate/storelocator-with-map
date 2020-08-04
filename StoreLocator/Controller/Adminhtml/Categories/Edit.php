<?php

namespace Brainvire\StoreLocator\Controller\Adminhtml\Categories;

use \Brainvire\StoreLocator\Controller\Adminhtml\Categories;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\Registry;
use \Brainvire\StoreLocator\Api\CategoryRepositoryInterface;
use \Brainvire\StoreLocator\Api\Data\CategoryInterfaceFactory;

class Edit extends Categories
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterfaceFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $resultPageFactory, $categoryRepository, $categoryFactory);
    }

    /**
     * Edit store
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('category_id');
        $category = $this->categoryFactory->create();

        if ($id) {
            try {
                $category = $this->categoryRepository->get($id);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This category no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $category->setData($data);
        }

        $this->coreRegistry->register('storelocator_category', $category);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Category') : __('Add New Category'),
            $id ? __('Edit Category') : __('Add New Category')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Store locator - Categories'));
        $resultPage->getConfig()->getTitle()
            ->prepend($category->getId() ? $category->getName() : __('Add New Category'));

        return $resultPage;
    }
}
