<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */


namespace Amasty\Xcoupon\Plugin\SalesRule\ResourceModel;

class Coupon
{
    /**
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon $subject
     * @param \Magento\SalesRule\Model\Rule $rule
     *
     * @return array
     */
    public function beforeUpdateSpecificCoupons(
        \Magento\SalesRule\Model\ResourceModel\Coupon $subject,
        \Magento\SalesRule\Model\Rule $rule
    ) {
        $rule->setOrigData('uses_per_customer', $rule->getUsesPerCustomer());
        $rule->setOrigData('uses_per_coupon', $rule->getUsesPerCoupon());

        return [$rule];
    }
}
