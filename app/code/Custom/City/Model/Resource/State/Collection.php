<?php

namespace Custom\City\Model\Resource\State;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Custom\City\Model\State',
            'Custom\City\Model\Resource\State'
        );
    }
}