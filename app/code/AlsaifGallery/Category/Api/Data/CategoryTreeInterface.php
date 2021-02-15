<?php

namespace AlsaifGallery\Category\Api\Data;

interface  CategoryTreeInterface 
{
    const KEY_THUMBNAIL_IMAGE = "thumbnail_image";
    const KEY_SLIDER = "slider";
    const KEY_BRANDS = "brands";
    const KEY_IMAGE = "image";
    const KEY_DESCRIPTION = "description";
    const KEY_ICON = "icon";
    const KEY_IS_FEATURED = "is_featured";
    
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get parent category ID
     *
     * @return int
     */
    public function getParentId();

    /**
     * Set parent category ID
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Get category name
     *
     * @return string
     */
    public function getName();

    /**
     * Set category name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);
    /**
     * Get category image
     *
     * @return string
     */
    // public function getImage();

    /**
     * Set category image
     *
     * @param string $name
     * @return $this
     */
    // public function setImage($image);
    
    /**
     * Get category thumbnail image
     *
     * @return string
     */
   // public function getThumbnailImage();

    /**
     * Set category thumbnail image
     *
     * @param string $thumbnailImage
     * @return $this
     */
   // public function setThumbnailImage($thumbnailImage);
 
    
    /**
     * Get category icon image
     *
     * @return string
     */
    public function getIcon();

    /**
     * Set category icon image
     *
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon);
    /**
     * Get category IsFeatured
     *
     * @return string
     */
    public function getIsFeatured();

    /**
     * Set category IsFeatured
     *
     * @param string $isFeatured
     * @return $this
     */
    public function setIsFeatured($isFeatured);

    
    /**
     * Check whether category is active
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsActive();

    /**
     * Set whether category is active
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get category position
     *
     * @return int
     */
    public function getPosition();

    /**
     * Set category position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Get category level
     *
     * @return int
     */
    public function getLevel();

    /**
     * Set category level
     *
     * @param int $level
     * @return $this
     */
    public function setLevel($level);

    /**
     * Get product count
     *
     * @return int
     */
    public function getProductCount();

    /**
     * Set product count
     *
     * @param int $productCount
     * @return $this
     */
    public function setProductCount($productCount);

    /**
     * @return \AlsaifGallery\Category\Api\Data\CategoryTreeInterface[]
     */
    public function getChildrenData();

    /**
     * @param \AlsaifGallery\Category\Api\Data\CategoryTreeInterface[] $childrenData
     * @return $this
     */
    public function setChildrenData(array $childrenData = null);
    /**
     * Get category slider
     *
     * @return \AlsaifGallery\Category\Api\Data\CategoryBannerInterface[]
     */
    public function getSlider();
    /**
     * Get category brands
     *
     * @return \AlsaifGallery\Category\Api\Data\CategoryBrandInterface[]
     */
    public function getBrands();
    /**
     * Get category $includeInMenu
     *
     * @return string[]
     */
    public function getIncludeInMenu();

    /**
     * Set category slider
     *
     * @param \AlsaifGallery\Category\Api\Data\CategoryBannerInterface[] $slider
     * @return $this
     */
    public function setSlider($slider);

    /**
     * Set category Brands
     *
     * @param \AlsaifGallery\Category\Api\Data\CategoryBrandInterface[] $brands
     * @return $this
     */
    public function setBrands($brands);
    
    /**
     * Set category $includeInMenu
     *
     * @param string[] $includeInMenu
     * @return $this
     */
    public function setIncludeInMenu($includeInMenu);
    
            /**
     * Get category Description
     *
     * @return string
     */
    // public function getDescription();

    /**
     * Set category name
     *
     * @param string $description
     * @return $this
     */
    // public function setDescription($description);
    
    
    
}
