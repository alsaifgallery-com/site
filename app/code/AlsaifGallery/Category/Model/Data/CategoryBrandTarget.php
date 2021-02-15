<?php
namespace AlsaifGallery\Category\Model\Data;

class CategoryBrandTarget extends \Magento\Framework\Api\AbstractSimpleObject  
                    implements \AlsaifGallery\Category\Api\Data\CategoryBrandTargetInterface

{
    /**
     * {@inheritdoc}
     */
    public function getKey() {
        return $this->_get(self::KEY_KEY);
    }
    /**
     * {@inheritdoc}
     */
    public function getValue() {
        return $this->_get( self::KEY_VALUE);   
    }
    /**
     * {@inheritdoc}
     */
    public function setKey($key){
        return $this->setData(self::KEY_KEY, $key);
    }
    /**
     * {@inheritdoc}
     */
    public function setValue($value) {
        return $this->setData(self::KEY_VALUE, $value);
    }

}