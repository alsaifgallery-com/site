<?php

namespace AlsaifGallery\Product\Model\Data;

class FilterItem extends \Magento\Framework\DataObject 
                     implements \AlsaifGallery\Product\Api\Data\FilterItemInterface {

    /**
     * {@inheritdoc}
     */
    public function getField() {
        return $this->getData(self::FIELD);
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
    public function setField($field) {
        return $this->setData(self::FIELD, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value) {
        return $this->setData(self::VALUE, $value);
    }

}
