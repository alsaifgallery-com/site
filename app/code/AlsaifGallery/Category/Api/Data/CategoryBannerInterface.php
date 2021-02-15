<?php

namespace AlsaifGallery\Category\Api\Data;

interface CategoryBannerInterface {
    const KEY_TARGET="target";
    const KEY_BANNER_ID="banner_id";
    const KEY_IMAGE="image";
    const KEY_POSITION="position";
    const KEY_NAME="name";
    const KEY_TITLE="title";
    
    /**
     *
     * @return string
     */
    public function getBannerId();
    /**
     * Set value for the given key
     *
     * @param string $bannerId
     * @return $this
     */
    public function setBannerId($bannerId);

    
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
    public function getPosition();
   /**
     * Set value for the given key
     *
     * @param  string $position
     * @return $this
     */
    public function setPosition($position);
    
    
    /**
     *
     * @return string
     */
    public function getName();
   /**
     * Set value for the given key
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);
    
    
    /**
     *
     * @return string
     */
    public function getTitle();
   /**
     * Set value for the given key
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);
    
    
    
    /**
     *
     * @return \AlsaifGallery\Category\Api\Data\CategoryBannerTargetInterface
     */         
    public function getTarget();
   /**
     * Set value for the given key
     *
     * @param \AlsaifGallery\Category\Api\Data\CategoryBannerTargetInterface $target
     * @return $this
     */
    public function setTarget( \AlsaifGallery\Category\Api\Data\CategoryBannerTargetInterface $target);
}
    