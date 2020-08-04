<?php
namespace Brainvire\StoreLocator\Block\Adminhtml\Category\Edit;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Data\FormFactory;
use \Brainvire\StoreLocator\Model\System\Config\IsActive;
use \Brainvire\StoreLocator\Block\Adminhtml\Category\Helper\Icon;

class Form extends Generic
{
    /**
     * @var IsActive
     */
    protected $isActive;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        IsActive $isActive,
        array $data = []
    ) {
        $this->isActive = $isActive;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('storelocator_category');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'      => 'edit_form',
                    'action'  => $this->getData('action'),
                    'method'  => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Informations')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'category_id',
                'hidden',
                ['name' => 'category_id']
            );
        }

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name'  => 'is_active',
                'options' => $this->isActive->toOptionArray()
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Name'),
                'required' => true
            ]
        );

        $fieldset->addType('icon', Icon::class);

        $fieldset->addField(
            'icon',
            'icon',
            [
                'name'  => 'icon',
                'label' => __('Category Icon'),
                'title' => __('Category Icon')
            ]
        );

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $data = $model->getData();
        $form->setValues($data);
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
