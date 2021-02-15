<?php

namespace Custom\City\Controller\Index;

use Custom\City\Controller\Zip;

class Zips extends Zip
{
    /**
     * Get zip codes and return json
     * @return mixed
     */
    public function execute()
    {
        $city = $this->getRequest()->getParam('city');
        $state_id = $this->getRequest()->getParam('state');
        $country_id = $this->getRequest()->getParam('country_id');
        $zip_codes_options = array();
        if($city!="" && $state_id!="" &&  $country_id!="" ){
            $city_id = $this->_cityFactory->create()->getCollection()
                ->addFieldToFilter('city',$city)
                ->addFieldToFilter('state_id',$state_id)
                ->addFieldToFilter('country_id',$country_id)
                ->getFirstItem()->getId();
            $zip_codes = $this->_zipFactory->create()->getCollection()
                ->addFieldToFilter('city_id',$city_id)
                ->addFieldToFilter('state_id',$state_id)
                ->addFieldToFilter('country_id',$country_id)
                ->addFieldToFilter('status',1);
            $zip_codes->getSelect()
                ->order('id DESC');
            if($zip_codes->count() > 0){
                foreach($zip_codes as $zip){
                    $zip_codes_options[] = $zip->getZipName();
                }
            }
        }elseif($city!="" && $country_id!="" && $state_id==""){
			$city_id = $this->_cityFactory->create()->getCollection()
                ->addFieldToFilter('city',$city)
                ->addFieldToFilter('country_id',$country_id)
                ->getFirstItem()->getId();
            $zip_codes = $this->_zipFactory->create()->getCollection()
                ->addFieldToFilter('city_id',$city_id)
                ->addFieldToFilter('country_id',$country_id)
                ->addFieldToFilter('status',1);
            $zip_codes->getSelect()
                ->order('id DESC');
            if($zip_codes->count() > 0){
                foreach($zip_codes as $zip){
                    $zip_codes_options[] = $zip->getZipName();
                }
            }
		}
        $result = $this->resultJsonFactory->create();
        $result->setData($zip_codes_options);
        return $result;
    }
}
