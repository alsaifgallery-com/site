<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Model\ResourceModel\Report\Usage\Grid;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * Class Collection
 * @package Amasty\Xcoupon\Model\ResourceModel\Report\Usage\Grid
 * @author Artem Brunevski
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->join(
            ['order' => 'sales_order'],
            'order.entity_id=main_table.entity_id',
            ['order.coupon_code']
        );

        $this->join(
            ['coupon' => 'salesrule_coupon'],
            'coupon.code=order.coupon_code',
            []
        );

        $this->join(
            ['rule' => 'salesrule'],
            'rule.rule_id=coupon.rule_id',
            ['rule.name']
        );

        $this->getSelect()->joinLeft(
            ['track' => $this->getTable('sales_shipment_track')],
            'track.order_id=main_table.entity_id',
            ['track_number' => 'GROUP_CONCAT(track.track_number)']
        );

        $this->getSelect()->group('main_table.entity_id');
    }

}