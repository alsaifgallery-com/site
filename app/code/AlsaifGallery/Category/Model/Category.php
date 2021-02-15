<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace AlsaifGallery\Category\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Convert\ConvertArray;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Profiler;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Catalog category
 *
 * @api
 * @method Category setAffectedProductIds(array $productIds)
 * @method array getAffectedProductIds()
 * @method Category setMovedCategoryId(array $productIds)
 * @method int getMovedCategoryId()
 * @method Category setAffectedCategoryIds(array $categoryIds)
 * @method array getAffectedCategoryIds()
 * @method Category setUrlKey(string $urlKey)
 * @method Category setUrlPath(string $urlPath)
 * @method Category getSkipDeleteChildren()
 * @method Category setSkipDeleteChildren(boolean $value)
 * @method Category setChangedProductIds(array $categoryIds) Set products ids that inserted or deleted for category
 * @method array getChangedProductIds() Get products ids that inserted or deleted for category
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Category extends \Magento\Catalog\Model\Category implements
\AlsaifGallery\Category\Api\Data\CategoryTreeInterface {

    /**
     * {@inheritdoc}
     */
    public function getDescription() {
        // return $this->getName();
        return $this->_getData(self::KEY_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage() {
        return $this->_getData(self::KEY_IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description) {
        return $this->setData(self::KEY_DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($image) {
        // return $this->getName();
        return $this->setData(self::KEY_IMAGE, $image);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlider() {
        return $this->_getData(self::KEY_SLIDER);
    }
    /**
     * {@inheritdoc}
     */
    public function getBrands() {
        return $this->_getData(self::KEY_BRANDS);
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnailImage() {
        return $this->_getData(self::KEY_THUMBNAIL_IMAGE);
    }
    /**
     * {@inheritdoc}
     */
    public function getIcon() {
        return $this->_getData(self::KEY_ICON);
    }
    /**
     * {@inheritdoc}
     */
    public function getIsFeatured() {
        return $this->_getData(self::KEY_IS_FEATURED);
    }

    /**
     * {@inheritdoc}
     */
    public function setSlider($slider) {
        return $this->setData(self::KEY_SLIDER, $slider);
    }
    /**
     * {@inheritdoc}
     */
    public function setBrands($brands) {
        return $this->setData(self::KEY_BRANDS, $brands);
    }

    /**
     * {@inheritdoc}
     */
    public function setThumbnailImage($thumbnailImage) {
        return $this->setData(self::KEY_THUMBNAIL_IMAGE, $thumbnailImage);
    }
    /**
     * {@inheritdoc}
     */
    public function setIcon($icon) {
        return $this->setData(self::KEY_ICON, $icon);
    }
    /**
     * {@inheritdoc}
     */
    public function setIsFeatured($isFeatured) {
        return $this->setData(self::KEY_IS_FEATURED, $isFeatured);
    }

}
