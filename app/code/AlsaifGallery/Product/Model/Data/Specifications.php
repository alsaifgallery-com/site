<?php

namespace AlsaifGallery\Product\Model\Data;

class Specifications extends \Magento\Framework\DataObject 
                     implements \AlsaifGallery\Product\Api\Data\SpecificationsInterface {

    /**
     * {@inheritdoc}
     */
    public function getKey() {
        return $this->getData(self::KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue() {
        return $this->getData(self::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($key) {
        return $this->setData(self::KEY, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value) {
        return $this->setData(self::VALUE, $value);
    }

}
