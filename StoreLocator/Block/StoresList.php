<?php
namespace Brainvire\StoreLocator\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\DataObject\IdentityInterface;
use \Brainvire\StoreLocator\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use \Brainvire\StoreLocator\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use \Magento\Framework\Json\Helper\Data as DataHelper;
use \Brainvire\StoreLocator\Helper\Config as ConfigHelper;
use \Brainvire\StoreLocator\Model\Category\Icon as CategoryIcon;
use \Brainvire\StoreLocator\Api\Data\StoreInterface;
use \Brainvire\StoreLocator\Model\ResourceModel\Store\Collection as StoreCollection;
use \Brainvire\StoreLocator\Model\Store;
use \Brainvire\StoreLocator\Model\Config\Source\GroupBy;

class StoresList extends Template implements IdentityInterface
{
    const XML_PATH_CONFIG_GOOGLE_KEY = 'storelocator/google_api_key/frontend';

    /**
     * @var \Brainvire\StoreLocator\Model\ResourceModel\Store\CollectionFactory
     */
    private $storeCollectionFactory;

    /**
     * @var \Brainvire\StoreLocator\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Brainvire\StoreLocator\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Brainvire\StoreLocator\Model\Category\Icon
     */
    private $categoryIcon;
    protected $_storeManager;
    protected $_urlInterface;
   

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Brainvire\StoreLocator\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory
     * @param \Brainvire\StoreLocator\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Framework\Json\Helper\Data $dataHelper
     * @param ConfigHelper $configHelper
     * @param CategoryIcon $categoryIcon
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        StoreCollectionFactory $storeCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        DataHelper $dataHelper,
        ConfigHelper $configHelper,
        CategoryIcon $categoryIcon,

        array $data = []
    ) {
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->_storeManager = $storeManager;      
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->dataHelper = $dataHelper;
        $this->_urlInterface = $urlInterface;
        $this->configHelper = $configHelper;
        $this->categoryIcon = $categoryIcon;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->_addBreadcrumbs();

        return parent::_prepareLayout();
    }

    private function _addBreadcrumbs()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'storelocator',
                [
                    'label' => __('Store Locator'),
                ]
            );
        }
    }

    /**
     * @return string
     */
    public function getStoreId()
    {
       
        return $this->_storeManager->getStore()->getId();
    }


    public function getStoresJson1()
    {
        if (!$this->hasData('stores_' . GroupBy::DONT_GROUP)) {
            $stores = [];

            $storesCollection = $this->storeCollectionFactory
                ->create();

            $storesCollection->addFilter('is_active', 1)
                  
                ->addOrder(
                    StoreInterface::NAME,
                    StoreCollection::SORT_ORDER_DESC
                );

            if ($storesCollection->getSize() > 0) {
              
                foreach ($storesCollection as $store) {
                    $elem = $store->getData();
                    $elem['country'] = $store->getCountry();
                    $elem['country_code'] = $store->getData('country');
                    $stores[] = $elem;
                }
            }

            $this->setData('stores_' . GroupBy::DONT_GROUP, $this->dataHelper->jsonEncode($stores));
        }

        return $this->getData('stores_' . GroupBy::DONT_GROUP);
    }

    public function getStoresJson($storeId)
    {
        if (!$this->hasData('stores_' . GroupBy::DONT_GROUP)) {
            $stores = [];

            $storesCollection = $this->storeCollectionFactory
                ->create();

            $storesCollection->addFilter('is_active', 1)
                    ->addFilter('store_ids',$storeId)
                ->addOrder(
                    StoreInterface::NAME,
                    StoreCollection::SORT_ORDER_DESC
                );

            if ($storesCollection->getSize() > 0) {
              
                foreach ($storesCollection as $store) {
                    $elem = $store->getData();
                    $elem['country'] = $store->getCountry();
                    $elem['country_code'] = $store->getData('country');
                    $stores[] = $elem;
                }
            }

            $this->setData('stores_' . GroupBy::DONT_GROUP, $this->dataHelper->jsonEncode($stores));
        }

        return $this->getData('stores_' . GroupBy::DONT_GROUP);
    }

    public function getStoresJsonById($countryid,$storeId)
    {
        //if (!$this->hasData('stores_' . GroupBy::DONT_GROUP)) {
            $stores = [];

            $storesCollection = $this->storeCollectionFactory->create();

            $storesCollection->addFilter('is_active', 1)
                ->addFilter('country',$countryid)
                ->addFilter('store_ids',$storeId);

            if ($storesCollection->getSize() > 0) {
              
                foreach ($storesCollection as $store) 
                    {
                   
                    $elem = $store->getData();
                    $elem['country'] = $store->getCountry();
                    $elem['country_code'] = $store->getData('country');
                    $stores[] = $elem;
                    }
                  
                
            }

            $this->setData('stores_' . 0, $this->dataHelper->jsonEncode($stores));
       // }

        return $this->getData('stores_' . 0);
    }

        public function getStores($storeId)
            {
               
                    $stores = [];

                    $storesCollection = $this->storeCollectionFactory
                        ->create();

                    $storesCollection->addFilter('is_active', 1)
                            ->addFilter('store_ids',$storeId)
                        ->addOrder(
                            StoreInterface::NAME,
                            StoreCollection::SORT_ORDER_DESC
                        );

                    if ($storesCollection->getSize() > 0) {
                      
                        foreach ($storesCollection as $store) {
                            $elem = $store->getData();
                            $elem['country'] = $store->getCountry();
                            $elem['country_code'] = $store->getData('country');
                            $stores[] = $elem;
                        }
                    }

                    $this->setData($stores);
                

                return $this->getData();
            }
    public function getStoresById($countryid,$storeId)
    {
         $stores = [];

            $storesCollection = $this->storeCollectionFactory->create();

            $storesCollection->addFilter('is_active', 1)
                ->addFilter('country',$countryid)
                ->addFilter('store_ids',$storeId);

            if ($storesCollection->getSize() > 0) {
              
                foreach ($storesCollection as $store) 
                    {
                   
                    $elem = $store->getData();
                    $elem['country'] = $store->getCountry();
                    $elem['country_code'] = $store->getData('country');
                    $stores[] = $elem;
                    }
                  
                
            }

            $this->setData($stores);
       // }

        return $this->getData();
    }

    /**
     * @return string
     */
    public function getStoresGroupedJson()
    {
        $groupBy = $this->getGroupStoresBy();

        if (!$this->hasData('stores_' . $groupBy)) {
            $stores = [];

            $storesCollection = $this->storeCollectionFactory
                ->create();

            $storesCollection->addFilter('is_active', 1)
                ->addFieldToSelect(['store_id', 'city_id', 'country']);

            if ($storesCollection->getSize() > 0) {
                switch ($groupBy) {
                    case GroupBy::COUNTRY:
                        $stores = $this->groupStoresByCountry($storesCollection);
                        break;
                    case GroupBy::CATEGORY:
                        $stores = $this->groupStoresByCategory($storesCollection);
                        break;
                    case GroupBy::COUNTRY_CATEGORY:
                        $stores = $this->groupStoresByCountryCategory($storesCollection);
                        break;
                    case GroupBy::CATEGORY_COUNTRY:
                        $stores = $this->groupStoresByCategoryCountry($storesCollection);
                        break;
                    default:
                        $stores = false;
                        break;
                }
            }

            $this->setData('stores_' . $groupBy, $this->dataHelper->jsonEncode($stores));
        }

        return $this->getData('stores_' . $groupBy);
    }

    /**
     * @return mixed
     */
    public function getCategoriesJson()
    {
        if (!$this->hasData('categories')) {
            $categories = [];
            $collection = $this->storeCollectionFactory->create();
            $collection->addFilter('is_active', 1);
            $collection->addFieldToSelect(['store_ids']);

            if (!empty($collection)) {
                foreach ($collection as $element) {
                    $categories[$element->getId()]['name'] = $element->getName();
                    
                }
            }

            $this->setData($categories);
        }

        return $this->getData();
    }

    /**
     * @return string|null
     */
    public function getGoogleApiKey()
    {
        return $this->configHelper->getGoogleApiKeyFrontend();
    }

    /**
     * @return int|null
     */
    public function getGroupStoresBy()
    {
        return $this->configHelper->getGroupStoresBy();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [Store::CACHE_TAG . '_' . 'list'];
    }

    /**
     * @param $collection
     * @return array
     */
    private function groupStoresByCountry($collection)
    {
        $stores = [];

        /**
         * @var \Brainvire\StoreLocator\Model\Store $store
         */
        foreach ($collection as $store) {
            $elemId = $store->getId();
            $stores[$store->getData('country')]['stores'][] = (int)$elemId;
            if (!array_key_exists('count', $stores[$store->getData('country')])) {
                $stores[$store->getData('country')]['name'] = $store->getCountry();
                $stores[$store->getData('country')]['count'] = 0;
                $stores[$store->getData('country')]['count_all'] = 0;
            }
            $stores[$store->getData('country')]['count']++;
            $stores[$store->getData('country')]['count_all']++;
        }

        return $stores;
    }

    /**
     * @param $collection
     * @return array
     */
    private function groupStoresByCategory($collection)
    {
        $stores = [];

        /**
         * @var \Brainvire\StoreLocator\Model\Store $store
         */
        foreach ($collection as $store) {
            $elemId = $store->getId();
            $stores[$store->getData('category_id')]['stores'][] = (int)$elemId;
            if (!array_key_exists('count', $stores[$store->getData('category_id')])) {
                $stores[$store->getData('category_id')]['name'] = $store->getCategoryName();
                $stores[$store->getData('category_id')]['count'] = 0;
                $stores[$store->getData('category_id')]['count_all'] = 0;
            }
            $stores[$store->getData('category_id')]['count']++;
            $stores[$store->getData('category_id')]['count_all']++;
        }

        return $stores;
    }



    /**
     * @param $collection
     * @return array
     */
    private function groupStoresByCountryCategory($collection)
    {
        $stores = [];

        /**
         * @var \Brainvire\StoreLocator\Model\Store $store
         */
        foreach ($collection as $store) {
            $elemId = $store->getId();
            $stores[$store->getData('country')]['elements'][$store->getData('category_id')]['stores'][] = (int)$elemId;
            if (!array_key_exists('count', $stores[$store->getData('country')])) {
                $stores[$store->getData('country')]['name'] = $store->getCountry();
                $stores[$store->getData('country')]['count'] = 0;
                $stores[$store->getData('country')]['count_all'] = 0;
            }
            if (!array_key_exists('count', $stores[$store->getData('country')]['elements'][$store->getData('category_id')])) {
                $stores[$store->getData('country')]['elements'][$store->getData('category_id')]['name'] = $store->getCategoryName();
                $stores[$store->getData('country')]['elements'][$store->getData('category_id')]['count'] = 0;
                $stores[$store->getData('country')]['elements'][$store->getData('category_id')]['count_all'] = 0;
            }
            $stores[$store->getData('country')]['count']++;
            $stores[$store->getData('country')]['count_all']++;
            $stores[$store->getData('country')]['elements'][$store->getData('category_id')]['count']++;
            $stores[$store->getData('country')]['elements'][$store->getData('category_id')]['count_all']++;
        }

        return $stores;
    }

    /**
     * @param $collection
     * @return array
     */
    private function groupStoresByCategoryCountry($collection)
    {
        $stores = [];

        /**
         * @var \Brainvire\StoreLocator\Model\Store $store
         */
        foreach ($collection as $store) {
            $elemId = $store->getId();
            $stores[$store->getData('category_id')]['elements'][$store->getData('country')]['stores'][] = (int)$elemId;
            if (!array_key_exists('count', $stores[$store->getData('category_id')])) {
                $stores[$store->getData('category_id')]['name'] = $store->getCategoryName();
                $stores[$store->getData('category_id')]['count'] = 0;
                $stores[$store->getData('category_id')]['count_all'] = 0;
            }
            if (!array_key_exists('count', $stores[$store->getData('category_id')]['elements'][$store->getData('country')])) {
                $stores[$store->getData('category_id')]['elements'][$store->getData('country')]['name'] = $store->getCountry();
                $stores[$store->getData('category_id')]['elements'][$store->getData('country')]['count'] = 0;
                $stores[$store->getData('category_id')]['elements'][$store->getData('country')]['count_all'] = 0;
            }
            $stores[$store->getData('category_id')]['count']++;
            $stores[$store->getData('category_id')]['count_all']++;
            $stores[$store->getData('category_id')]['elements'][$store->getData('country')]['count']++;
            $stores[$store->getData('category_id')]['elements'][$store->getData('country')]['count_all']++;
        }

        return $stores;
    }


    public function getStoreCollect()
    {
        
       
            $stores = [];

            $storesCollection = $this->storeCollectionFactory
                ->create();

            $storesCollection->addFilter('is_active', 1)
                ->addOrder(
                    StoreInterface::NAME,
                    StoreCollection::SORT_ORDER_ASC
                );

            if ($storesCollection->getSize() > 0) {
                foreach ($storesCollection as $store) {
                    $elem = $store->getData();
                    $elem['country'] = $store->getCountry();
                    $elem['country_code'] = $store->getData('country');
                    $stores[] = $elem;
                }
            }

            $this->setData($stores);
        

        return $this->getData();
    }



    public function getCountryList()
    {
            $stores = [];

            $storesCollection = $this->storeCollectionFactory->create();

            $storesCollection->addFilter('is_active', 1)
                ->addOrder(StoreInterface::COUNTRY,StoreCollection::SORT_ORDER_ASC);

            if ($storesCollection->getSize() > 0) {
                foreach ($storesCollection as $store) 
                {
                   
                    $elem['country'] = $store->getCountry();
                    $elem['country_code'] = $store->getData('country');
                    $stores[] = $elem;
                }
            }

            $this->setData($stores);
            return $this->getData();
    }
    public function getCountryListByStoreId($storeId)
    {

            $stores = [];

            $storesCollection = $this->storeCollectionFactory->create();

            $storesCollection
                    ->addFilter('is_active', 1)
                    ->addFilter('store_ids',$storeId)
                    ->addOrder(StoreInterface::COUNTRY,StoreCollection::SORT_ORDER_ASC);

            if ($storesCollection->getSize() > 0) {
                foreach ($storesCollection as $store) 
                {
                   
                    $elem['country'] = $store->getCountry();
                    $elem['country_code'] = $store->getData('country');
                    $stores[] = $elem;
                }
            }

            $this->setData($stores);
            return $this->getData();
    }

    public function getPasscountryid()
    {
        $this->getRequest()->getParams();
        $countryid = $this->getRequest()->getParam('countryid');
        return $countryid;
    }

    public function getCityList($storeId)
    {
            $stores = [];

            $storesCollection = $this->storeCollectionFactory->create();

            $storesCollection->addFilter('is_active', 1)
                ->addFilter('store_ids',$storeId)
                ->addOrder('city',StoreCollection::SORT_ORDER_ASC);

            if ($storesCollection->getSize() > 0) {
                foreach ($storesCollection as $store) 
                {
                   
                    $elem['city'] = $store->getCity();
                    $stores[] = $elem;
                }
            }

            $this->setData($stores);
            return $this->getData();
    }

    public function getCityListById($countryid,$storeId)
    {
            $countryid = $this->getRequest()->getParam('countryid');
            $stores = [];

            $storesCollection = $this->storeCollectionFactory->create();

            $storesCollection
                ->addFilter('is_active', 1)
                ->addFilter('country',$countryid)
                ->addFilter('store_ids',$storeId)
                ->addOrder('city',StoreCollection::SORT_ORDER_ASC);

            if ($storesCollection->getSize() > 0) {
                foreach ($storesCollection as $store) 
                {                   
                    $elem['city'] = $store->getCity();
                    $stores[] = $elem;
                }
            }

            $this->setData($stores);
            return $this->getData();
    }

    public function getPasscityid()
    {
        $this->getRequest()->getParams();
        $cityid = $this->getRequest()->getParam('cityid');
        return $cityid;
    }
    public function getLocationList($storeId)
    {
            
            $stores = [];

            $storesCollection = $this->storeCollectionFactory->create();

            $storesCollection
                ->addFilter('is_active', 1)
                ->addFilter('store_ids',$storeId)
                ->addOrder('location',StoreCollection::SORT_ORDER_ASC);

            if ($storesCollection->getSize() > 0) {
                foreach ($storesCollection as $store) 
                {
                   
                    $elem['location'] = $store->getLocation();
                    $stores[] = $elem;
                }
            }

            $this->setData($stores);
            return $this->getData();
    }

    public function getLocationListById($countryid,$cityId,$storeId)
    {
            
            $stores = [];

            $storesCollection = $this->storeCollectionFactory->create();


            if($countryid == "")
            {
                $storesCollection
                ->addFilter('is_active', 1)
                ->addFilter('store_ids',$storeId)
                ->addFilter('city',$cityId)
                ->addOrder('location',StoreCollection::SORT_ORDER_ASC);
            }
            else if($cityId == "")
            {
                $storesCollection
                ->addFilter('is_active', 1)
                ->addFilter('store_ids',$storeId)
                ->addFilter('country',$countryid)
                ->addOrder('location',StoreCollection::SORT_ORDER_ASC);
            
            }
            else
            {
                 $storesCollection
                ->addFilter('is_active', 1)
                ->addFilter('store_ids',$storeId)
                ->addFilter('country',$countryid)
                ->addFilter('city',$cityId)
                ->addOrder('location',StoreCollection::SORT_ORDER_ASC);
            }
            

            if ($storesCollection->getSize() > 0) {
                foreach ($storesCollection as $store) 
                {
                   
                    $elem['location'] = $store->getLocation();
                    $stores[] = $elem;
                }
            }

            $this->setData($stores);
            return $this->getData();
    }


    public function getNameList($storeId)
    {
            
            $stores = [];

            $storesCollection = $this->storeCollectionFactory->create();

            $storesCollection
                ->addFilter('is_active', 1)
                ->addFilter('store_ids',$storeId)
                ->addOrder('name',StoreCollection::SORT_ORDER_ASC);

            if ($storesCollection->getSize() > 0) {
                foreach ($storesCollection as $store) 
                {
                   
                    $elem['name'] = $store->getName();
                    $stores[] = $elem;
                }
            }

            $this->setData($stores);
            return $this->getData();
    }

    public function getNameListById($countryid,$cityId,$storeId)
    {
            
            $stores = [];

            $storesCollection = $this->storeCollectionFactory->create();

           if($countryid == "")
            {
                $storesCollection
                ->addFilter('is_active', 1)
                ->addFilter('store_ids',$storeId)
                ->addFilter('city',$cityId)
                ->addOrder('name',StoreCollection::SORT_ORDER_ASC);
            }
            else if($cityId == "")
            {
                $storesCollection
                ->addFilter('is_active', 1)
                ->addFilter('store_ids',$storeId)
                ->addFilter('country',$countryid)
                ->addOrder('name',StoreCollection::SORT_ORDER_ASC);
            
            }
            else
            {
                 $storesCollection
                ->addFilter('is_active', 1)
                ->addFilter('store_ids',$storeId)
                ->addFilter('country',$countryid)
                ->addFilter('city',$cityId)
                ->addOrder('name',StoreCollection::SORT_ORDER_ASC);
            }
            

            if ($storesCollection->getSize() > 0) {
                foreach ($storesCollection as $store) 
                {
                   
                    $elem['name'] = $store->getName();
                    $stores[] = $elem;
                }
            }

            $this->setData($stores);
            return $this->getData();
    }




    public function getModuleUrl()
    {
        //echo $this->_urlInterface->getCurrentUrl() . '<br />';
        
        //echo $this->_urlInterface->getUrl() . '<br />';
        
        //echo $this->_urlInterface->getBaseUrl() . '<br />';
        $url = $this->_urlInterface->getUrl('store-locator');
        return $url;
    }


}
