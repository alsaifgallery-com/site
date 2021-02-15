<?php

namespace Custom\City\Controller\Index;
use Custom\City\Controller\City;
class Cities extends City
{
    /*
     * Get Cities and return in json format
     */
    public function execute()
    {
        $state_id = $this->getRequest()->getParam('state');
        $country_id = $this->getRequest()->getParam('country_id');
        $cities = array();

		$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$storeCode = $storeManager->getStore()->getCode();
    $locale = substr($storeCode, -2);

        $cities_indexes = array();
        if( $state_id!="" && $country_id!=""){
            $cities_options = $this->_cityFactory->create()->getCollection()
                ->addFieldToFilter('state_id',$state_id)
                ->addFieldToFilter('country_id',$country_id)
                ->addFieldToFilter('status',1);
            $cities_options->getSelect()
                ->order('city ASC');
            if($cities_options->count() > 0){

				if($locale == 'en') {
					foreach($cities_options as $city){
						$cities[] = $city->getCity();
						$cities_indexes[] = $city->getCity();
						}
				} else if($locale == 'ar') {
					foreach($cities_options as $city){
						$cities[] = $city->getArabicName();
						$cities_indexes[] = $city->getArabicName();
					}
				}

				/*
                foreach($cities_options as $city){
                    $cities[] = __($city->getCity());
                    $cities_indexes[] = $city->getCity();
                } */
            }
        }elseif($state_id=="" && $country_id!=""){
            $cities_options = $this->_cityFactory->create()->getCollection()
                ->addFieldToFilter('country_id',$country_id)
                ->addFieldToFilter('status',1);
            $cities_options->getSelect()
                ->order('city ASC');
            if($cities_options->count() > 0){
				if($sid == 2) {
					foreach($cities_options as $city){
						$cities[] = $city->getCity();
						$cities_indexes[] = $city->getCity();
						}
				} else if($sid == 3) {
					foreach($cities_options as $city){
						$cities[] = $city->getArabicName();
						$cities_indexes[] = $city->getArabicName();
					}
				} /*
                foreach($cities_options as $city){
                    $cities[] = __($city->getCity());
                    $cities_indexes[] = $city->getCity();
                }*/
            }
        }
        $result = $this->resultJsonFactory->create();
        $result->setData(array('cities'=>$cities,'cities_indexes'=>$cities_indexes));
        return $result;
    }
}
