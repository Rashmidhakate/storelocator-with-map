<?php

namespace Brainvire\StoreLocator\Block\Adminhtml\Category;

use \Magento\Backend\Block\Widget\Form\Container;
use \Magento\Backend\Block\Widget\Context;
use \Magento\Framework\Registry;

class Edit extends Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize store edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'category_id';
        $this->_blockGroup = 'Brainvire_StoreLocator';
        $this->_controller = 'adminhtml_category';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Category'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete category'));
    }

    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('storelocator_category')->getId()) {
            return __("Edit Category '%1'", $this->escapeHtml($this->coreRegistry->registry('storelocator_category')->getName()));
        } else {
            return __('Add Category');
        }
    }
}
