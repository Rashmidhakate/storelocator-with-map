<?php
namespace Brainvire\StoreLocator\Block\Adminhtml\Stores\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Data\FormFactory;
use \Brainvire\StoreLocator\Model\Config\Source\Country;
use \Brainvire\StoreLocator\Model\System\Config\IsActive;
use \Brainvire\StoreLocator\Block\Adminhtml\Stores\Helper\GoogleMap;

class Map extends Generic
{
    /**
     * @var Country
     */
    private $country;
    private $isActive;
    /**
     * Map constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Country $country
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Country $country,
        IsActive $isActive,
        array $data = []
    ) {
        $this->country = $country;
         $this->isActive = $isActive;
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
            'map_fieldset',
            ['legend' => __('Other Informations')]
        );

        $fieldset->addType('google_map', GoogleMap::class);

        
         $fieldset->addField(
            'country',
            'select',
            [
                'name'     => 'country',
                'label'    => __('Country'),
                'options'  => $this->country->toOptionArray(),
                'required' => true
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name'     => 'city',
                'label'    => __('City'),
                'required' => true,
                'class' => 'validate-alpha'
            ]
        );

        $fieldset->addField(
            'location',
            'text',
            [
                'name'     => 'location',
                'label'    => __('Location'),
                'required' => true,
                'class' => 'validate-alphanumeric'
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Name'),
                'required' => true,
                'class' => 'validate-alphanumeric'
            ]
        );

        $fieldset->addField(
            'address',
            'textarea',
            [
                'name'     => 'address',
                'label'    => __('Address'),
                'required' => true,
               // 'class' => 'validate-street'
            ]
        );


        $fieldset->addField(
            'postcode',
            'text',
            [
                'name'     => 'postcode',
                'label'    => __('Zip Code'),
                'required' => false,
                'class' => 'validate-zip-international',
            ]
        );

       

        $fieldset->addField(
            'email',
            'text',
            [
                'name'     => 'email',
                'label'    => __('E-mail'),
                'required' => false,
                'class' => 'validate-email'
            ]
        );

        $fieldset->addField(
            'phone',
            'text',
            [
                'name'     => 'phone',
                'label'    => __('Phone Number'),
                'required' => true,
               
            ]
        );

        $fieldset->addField(
            'fax',
            'text',
            [
                'name'     => 'fax',
                'label'    => __('Fax'),
                'required' => false,
                'class' => 'validate-fax'
            ]
        );

        // $fieldset->addField(
        //     'working_from',
        //     'date',
        //     [
        //         'name'     => 'working_from',
        //         'label'    => __('Working From'),
        //         'required' => true,
        //         'date_format' => 'yyyy-MM-dd',
        //         'time_format' => 'HH:mm:ss',
        //         'options' =>[
        //             'showsDate' => false,
        //             'showsTime'=> true,
        //             'timeOnly'=>true
        //        ],
        //     ]
        // );
        $fieldset->addField(
            'working_to',
            'text',
            [
                'name'     => 'working_to',
                'label'    => __('Working Hours'),
                'required' => true,
                
               
            ]
        );

        //  $fieldset->addField(
        //     'hours',
        //     'hidden',
        //     ['name' => 'hours']
        // );

        // $fieldset->addField(
        //     'clone-hours',
        //     '\Brainvire\StoreLocator\Block\Adminhtml\Manage\Renderer\Hours',
        //     [
        //         'label' => __('Working Hours'),
        //         'name' => 'clone-hours'
        //     ]
        // );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label'   => __('Status'),
                'title'   => __('Status'),
                'name'    => 'is_active',
                'options' => $this->isActive->toOptionArray()
            ]
        );


        $fieldset->addField(
            'lat',
            'text',
            [
                'name'     => 'lat',
                'label'    => __('Latitude'),
                'required' => true,
                'class' => 'validate-number',
                
            ]
        );

        $fieldset->addField(
            'lng',
            'text',
            [
                'name'     => 'lng',
                'label'    => __('Longitude'),
                'required' => true,
                'class' => 'validate-number',
            ]
        );

        $fieldset->addField(
            'zoom',
            'text',
            [
                'name'     => 'zoom',
                'label'    => __('Zoom'),
                'required' => true,
                'class' => 'validate-number'
            ]
        );

        $fieldset->addField(
            'store_location',
            'google_map',
            [
                'name'  => 'store_location',
                'label' => __('Store Location'),
                'title' => __('Store Location')
            ]
        );

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }
        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
