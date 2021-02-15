<?php

namespace AlsaifGallery\Category\Api\Data;

interface CategoryBrandInterface {
    const KEY_TARGET="target";
    const KEY_BRAND_ID="brand_id";
    const KEY_IMAGE="image";
    const KEY_OPTION_ID="option_id";
    const KEY_ATTRIBUTE_ID="attribute_id";
    const KEY_TITLE="brand_title";
    const KEY_URL_KEY="url_key";
    
    
    /**
     *
     * @return string
     */
    public function getBrandId();
    /**
     * Set value for the given key
     *
     * @param string $brandId
     * @return $this
     */
    public function setBrandId($brandId);

    
    /**
     *
     * @return string
     */
    public function getImage();
    /**
     * Set value for the given key
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image);
    
    
    
    /**
     *
     * @return string
     */
    public function getOptionId();
   /**
     * Set value for the given key
     *
     * @param  string $optionId
     * @return $this
     */
    public function setOptionId($optionId);
    
    
    /**
     *
     * @return string
     */
    public function getAttributeId();
   /**
     * Set value for the given key
     *
     * @param string $attributeId
     * @return $this
     */
    public function setAttributeId($attributeId);
    
    
    /**
     *
     * @return string
     */
    public function getBrandTitle();
   /**
     * Set value for the given key
     *
     * @param string $title
     * @return $this
     */
    public function setBrandTitle($title);
    
    /**
     *
     * @return string
     */
    public function getUrlKey();
    
   /**
     * Set value for the given key
     *
     * @param string $urlKey
     * @return $this
     */
    public function setUrlKey($urlKey);
    
    
    
    /**
     *
     * @return \AlsaifGallery\Category\Api\Data\CategoryBrandTargetInterface
     */         
    public function getTarget();
   /**
     * Set value for the given key
     *
     * @param \AlsaifGallery\Category\Api\Data\CategoryBrandTargetInterface $target
     * @return $this
     */
    public function setTarget( \AlsaifGallery\Category\Api\Data\CategoryBrandTargetInterface $target);
}
    