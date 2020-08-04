<?php

namespace Brainvire\StorePickupMethod\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class CustomConfigProvider implements ConfigProviderInterface
{

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    public function getStates()
    {
        $adapter = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $select = $adapter->select()
                    ->from('inventory_source');
        return $adapter->fetchAll($select);
    }

    public function getConfig()
    {   
        $storepick_config = array();
        $storepick_config[] = array(
                'source_code' => "",
                'name' => "please select source"
        );
        foreach ($this->getStates() as $field) {
            $storepick_config[] = array(
                'source_code' => $field['source_code'],
                'name' => $field['name']
            );
        }     
        $config = [
            'storepick_config' => $storepick_config,
            'storepick_config_encode' => json_encode($storepick_config),
        ];
        return $config;
    }


}
