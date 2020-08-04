<?php
namespace Brainvire\StoreLocator\Controller\Adminhtml\Categories;

use \Brainvire\StoreLocator\Controller\Adminhtml\Categories;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Brainvire\StoreLocator\Api\CategoryRepositoryInterface;
use \Magento\Backend\Model\View\Result\ForwardFactory;
use \Magento\Framework\App\ResponseInterface;
use \Brainvire\StoreLocator\Api\Data\CategoryInterfaceFactory;

class NewAction extends Categories
{
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Brainvire\StoreLocator\Api\CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterfaceFactory $categoryFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $resultPageFactory, $categoryRepository, $categoryFactory);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
