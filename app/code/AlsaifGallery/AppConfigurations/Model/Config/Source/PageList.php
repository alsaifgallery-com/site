<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AlsaifGallery\AppConfigurations\Model\Config\Source;

/**
 * Description of PageList
 *
 * @author nada
 */
class PageList implements \Magento\Framework\Option\ArrayInterface {
    //put your code here
    
    public $pageCollection;
    
    
    public function __construct(
            \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollection
    ) {
        $this->pageCollection = $pageCollection;
    }

    public function toOptionArray()
    {
           $collection=$this->pageCollection->create();
        $collection->addFieldToFilter('is_active' , \Magento\Cms\Model\Page::STATUS_ENABLED);
        foreach($collection as $page){
            
		   $data['value'] = $page->getData('page_id');
	           $data['label'] = $page->getData('title');
		   $res[] = $data;
		} 
                
                
                return $res;

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
