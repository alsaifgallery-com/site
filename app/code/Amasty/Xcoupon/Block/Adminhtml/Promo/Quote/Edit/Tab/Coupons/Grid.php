<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */


namespace Amasty\Xcoupon\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons;

class Grid extends \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Grid
{
    protected function _prepareColumns()
    {
        $this->addColumnAfter(
            'usage_limit',
            ['header' => __('Uses per Coupon'), 'index' => 'usage_limit'],
            'used'
        );

        $this->addColumnAfter(
            'usage_per_customer',
            ['header' => __('Uses per Customer'), 'index' => 'usage_per_customer'],
            'used'
        );
        if ($id = $this->getRequest()->getParam('id')) {
            $this->addColumnAfter(
                'edit',
                [
                    'header' => __('Edit'),
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => [
                        [
                            'caption' => __('Edit'),
                            'url' => [
                                'base' => 'amasty_xcoupon/sales_rule/editCoupon',
                                'params' => [
                                    'store' => $this->getRequest()->getParam('store'),
                                    'ruleId' => $id
                                ]
                            ],
                            'field' => 'id'
                        ]
                    ],
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'edit',
                    'header_css_class' => 'col-action',
                    'column_css_class' => 'col-action'
                ],
                'times_used'
            );
        }

        parent::_prepareColumns();
    }
}