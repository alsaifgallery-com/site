<?php

namespace AlsaifGallery\RestAPIConfig\Model\Config\Source;

class NotVerifiedStatus implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [['value' => '', 'label' => __(' ')]];
    }

    public function toArray()
    {
        return [' ' => __(' ')];
    }
}
