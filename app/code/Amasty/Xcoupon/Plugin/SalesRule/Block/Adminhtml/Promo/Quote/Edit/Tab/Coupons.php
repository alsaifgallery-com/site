<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Plugin\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab;

/**
 * Class Coupons
 * @package Amasty\Xcoupon\Plugin\Block\Adminhtml\Promo\Quote\Edit\Tab
 * @author Artem Brunevski
 */
class Coupons
{
    /**
     * @param \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons $subject
     * @param \Closure $proceed
     * @param $alias
     * @param string $childChildAlias
     * @param bool|true $useCache
     * @return string
     */
    public function aroundGetChildHtml(
        \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons $subject,
        \Closure $proceed,
        $alias,
        $childChildAlias = '',
        $useCache = true
    ){
        $result = $proceed($alias, $childChildAlias, $useCache);

        if ($alias === 'promo_quote_edit_tab_coupons_form'){
            $result =
                $subject->getChildHtml('amasty_xcoupon_promo_quote_edit_tab_coupons_import') .
                $subject->getChildHtml('amasty_xcoupon_promo_quote_edit_tab_coupons_generate') .
                $result;
        }

        return $result;
    }
}