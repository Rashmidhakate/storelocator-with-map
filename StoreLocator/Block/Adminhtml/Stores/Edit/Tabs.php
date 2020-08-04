<?php
namespace Brainvire\StoreLocator\Block\Adminhtml\Stores\Edit;

use \Brainvire\StoreLocator\Block\Adminhtml\Stores\Edit\Tab\Info;
use \Brainvire\StoreLocator\Block\Adminhtml\Stores\Edit\Tab\Map;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('storelocator_stores_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Store Edit'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'store_info',
            [
                'label' => __('Store Selection'),
                'title' => __('Store Selection'),
                'content' => $this->getLayout()->createBlock(
                    Info::class
                )->toHtml(),
                'active' => true
            ]
        );

        $this->addTab(
            'map_info',
            [
                'label' => __('Other Informations'),
                'title' => __('Other Informations'),
                'content' => $this->getLayout()->createBlock(
                    Map::class
                )->toHtml(),
                'active' => false
            ]
        );

        return parent::_beforeToHtml();
    }
}
