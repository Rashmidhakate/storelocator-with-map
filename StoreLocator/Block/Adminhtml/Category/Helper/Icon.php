<?php
namespace Brainvire\StoreLocator\Block\Adminhtml\Category\Helper;

use \Magento\Framework\Data\Form\Element\Image as ImageField;
use \Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use \Magento\Framework\Data\Form\Element\CollectionFactory as ElementCollectionFactory;
use \Magento\Framework\Escaper;
use \Brainvire\StoreLocator\Model\Category\Icon as CategoryIcon;
use \Magento\Framework\UrlInterface;

class Icon extends ImageField
{
    /**
     * @var \Brainvire\StoreLocator\Model\Category\Icon
     */
    protected $iconModel;

    /**
     * @param CategoryIcon $iconModel
     * @param ElementFactory $factoryElement
     * @param ElementCollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        CategoryIcon $iconModel,
        ElementFactory $factoryElement,
        ElementCollectionFactory $factoryCollection,
        Escaper $escaper,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->iconModel = $iconModel;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $urlBuilder, $data);
    }

    /**
     * Get icon preview url
     *
     * @return string|bool
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->iconModel->getBaseUrl() . $this->getValue();
        }
        return $url;
    }
}
