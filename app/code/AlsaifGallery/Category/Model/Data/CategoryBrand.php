<?php

namespace AlsaifGallery\Category\Model\Data;

class CategoryBrand extends \Magento\Framework\Api\AbstractSimpleObject implements \AlsaifGallery\Category\Api\Data\CategoryBrandInterface {

    /**
     * {@inheritdoc}
     */
    public function getBrandId() {
        return $this->_get(self::KEY_BRAND_ID);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUrlKey() {
        return $this->_get(self::KEY_URL_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage() {
        return $this->_get(self::KEY_IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeId() {
        return $this->_get(self::KEY_ATTRIBUTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionId() {
        return $this->_get(self::KEY_OPTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget() {
        return $this->_get(self::KEY_TARGET);
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandTitle() {
        return $this->_get(self::KEY_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBrandId($brandId) {
        return $this->setData(self::KEY_BRAND_ID, $brandId);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($image) {
        return $this->setData(self::KEY_IMAGE, $image);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlKey($urlKey){
        return $this->setData(self::KEY_URL_KEY, $urlKey);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptionId($optionId) {
        return $this->setData(self::KEY_OPTION_ID, $optionId);
    }

    /**
     * {@inheritdoc}
     */
    public function setTarget(\AlsaifGallery\Category\Api\Data\CategoryBrandTargetInterface $target) {
        return $this->setData(self::KEY_TARGET, $target);
    }

    /**
     * {@inheritdoc}
     */
    public function setBrandTitle($title) {
        return $this->setData(self::KEY_TITLE, $title);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setAttributeId($attributeId) {
         return $this->setData(self::KEY_ATTRIBUTE_ID, $attributeId);
    }

}
