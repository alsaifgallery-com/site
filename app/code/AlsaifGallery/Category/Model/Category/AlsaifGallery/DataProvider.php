<?php
namespace AlsaifGallery\Category\Model\Category\AlsaifGallery;
  
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
  
    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'thumbnail_image';// custom image field
        $fields['content'][] = 'icon';// custom image field
        $fields['content'][] = 'slider';// custom image field
         
        return $fields;
    }
}
