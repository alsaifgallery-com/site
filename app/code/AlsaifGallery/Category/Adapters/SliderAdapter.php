<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AlsaifGallery\Category\Adapters;

use Mageplaza\BannerSlider\Model\ResourceModel\Slider\CollectionFactory;
/**
 * Description of Slider
 *
 * @author helal
 */
class SliderAdapter implements SliderInterface {
   
        /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Sliders constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
            CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;

    }
    
    public function getSliders() {
        return $this->collectionFactory->create()->addActiveFilter();
    }
    
    public function getSlider($sliderId){
        return $this->getSliders()->addIdFilter($sliderId)->getFirstItem();
    }
    
    public function getSliderBannerCollection($sliderId){
        $slider =  $this->getSlider($sliderId);
        if($slider->getId()){
            return $slider ->getSelectedBannersCollection()
                           ->addFieldToFilter('status',1)
                           ->addOrder('position', 'ASC');
        }
        return false;
    }
    
   
}
