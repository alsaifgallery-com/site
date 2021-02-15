<?php

namespace AlsaifGallery\Category\Model\Data;

class CategoryBanner extends \Magento\Framework\Api\AbstractSimpleObject implements \AlsaifGallery\Category\Api\Data\CategoryBannerInterface {

    /**
     * {@inheritdoc}
     */
    public function getBannerId() {
        return $this->_get(self::KEY_BANNER_ID);
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
    public function getName() {
        return $this->_get(self::KEY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition() {
        return $this->_get(self::KEY_POSITION);
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
    public function getTitle() {
        return $this->_get(self::KEY_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBannerId($bannerId) {
        return $this->setData(self::KEY_BANNER_ID, $bannerId);
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
    public function setName($name) {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position) {
        return $this->setData(self::KEY_POSITION, $position);
    }

    /**
     * {@inheritdoc}
     */
    public function setTarget(\AlsaifGallery\Category\Api\Data\CategoryBannerTargetInterface $target) {
        return $this->setData(self::KEY_TARGET, $target);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title) {
        return $this->setData(self::KEY_TITLE, $title);
    }

}
