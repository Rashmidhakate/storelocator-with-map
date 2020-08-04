<?php
namespace Brainvire\StoreLocator\Block\Adminhtml\Stores\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Data\FormFactory;
use \Brainvire\StoreLocator\Model\System\Config\IsActive;
use \Brainvire\StoreLocator\Model\StoreLocator\System\Config\Categories;
use \Brainvire\StoreLocator\Model\Config\Source\Country;


class Info extends Generic
{
    /**
     * @var IsActive
     */
    private $isActive;

    /**
     * @var Categories
     */
    private $categories;

    /**
     * @var Country
     */
    private $country;
    private $systemStore;

    /**
     * Info constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param IsActive $isActive
     * @param Categories $categories
     * @param Country $country
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\System\Store $systemStore,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        IsActive $isActive,
        Categories $categories,
        Country $country,
        array $data = []
    ) {
        $this->isActive = $isActive;
        $this->categories = $categories;
        $this->country = $country;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * View URL getter
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getViewUrl($storeId)
    {
        return $this->getUrl('storelocator/*/*', ['store_id' => $storeId]);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('storelocator_store');

        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Store Selection')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'store_id']
            );
        }



        // $fieldset->addField(
        //     'category_id',
        //     'select',
        //     [
        //         'name'     => 'category_id',
        //         'label'    => __('Category'),
        //         'options'  => $this->categories->toOptionArray(),
        //         'required' => true
        //     ]
        // );

         $fieldset->addField(
           'store_ids',
           'multiselect',
           [
             'name'     => 'store_ids[]',
             'label'    => __('Store Views'),
             'title'    => __('Store Views'),
             'required' => true,
             'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
           ]
        );

        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
