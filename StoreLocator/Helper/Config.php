<?php
namespace Brainvire\StoreLocator\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    /**
     * @return string|null
     */
    public function getGoogleApiKeyFrontend()
    {
        return $this->scopeConfig->getValue('storelocator/google_api_key/frontend', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @return string|null
     */
    public function getGoogleApiKeyBackend()
    {
        return $this->scopeConfig->getValue('storelocator/google_api_key/backend', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @return int|null
     */
    public function getGroupStoresBy()
    {
        return $this->scopeConfig->getValue('storelocator/configuration/group_by', ScopeInterface::SCOPE_STORE);
    }
}
