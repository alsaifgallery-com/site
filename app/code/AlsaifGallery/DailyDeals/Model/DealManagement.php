<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AlsaifGallery\DailyDeals\Model;
use Mageplaza\DailyDeal\Model\DealFactory;

/**
 * Description of Deal
 *
 * @author nada
 */
class DealManagement implements \AlsaifGallery\DailyDeals\Api\DealManagementInterface {
    public $_dealFactory;
    
    
    public $_data;
    
    
    public function __construct(DealFactory $dealFactory, \Mageplaza\DailyDeal\Helper\Data $data) {
        $this->_dealFactory = $dealFactory;
        
        $this->_data=$data;
    }
    //put your code here
     /**
     * {@inheritdoc}
      * 
     */
    public function getProductDeal($productId){
        $deals=[];
        $dealCollection = $this->_dealFactory->create()->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('product_id', ['eq' => $productId]);
   
        if($this->_data->checkDealProduct($productId)){
           array_push($deals,$dealCollection->getFirstItem()->convertToArray());  
           return $deals;
        }else{
            return $deals;
        }
        
}
 

    }
