<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigLIst
 *
 * @author nada
 */
namespace AlsaifGallery\AppConfigurations\Model\Config\Source;

use  Magento\Store\Model\ScopeInterface;
 use Magento\Framework\DB\Adapter\Pdo\Mysql;

class ConfigList implements \Magento\Framework\Option\ArrayInterface{
    //put your code here
    public $storeManager;
    
    public $objectManager;
    
    public $configScopeConfigInterface;
    
    public $scopeInterface;
    
    public function __construct(

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $configScopeConfigInterface

    ) {
        $this->_storeManager = $storeManager;
        $this->_scopeConfigInterface = $configScopeConfigInterface;
    }
    public function getConfigData() {
        

        $storeId = $this->_storeManager->getStore()->getId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource_track = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection_track = $resource_track->getConnection();
        $tableName_track = $resource_track->getTableName('core_config_data');
        $sql = "SELECT * FROM  " . $tableName_track . " where scope_id =0 ";
        $data = $connection_track->fetchAll($sql);
        return $data;
    }

    public function toOptionArray()
    {
        $arr=[];
        $configuirations=[];
        foreach ($this->getConfigData() as $data){
           $arr['value']=$data['path'];
           $arr['label']=$data['path'];
           array_push($configuirations,$arr);
        }
         return $configuirations;

    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('label'), 1 => __('Value')];
    }
}
