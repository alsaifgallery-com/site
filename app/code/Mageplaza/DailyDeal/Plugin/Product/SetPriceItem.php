<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_DailyDeal
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DailyDeal\Plugin\Product;

use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Mageplaza\DailyDeal\Helper\Data as HelperData;

/**
 * Class SetPriceItem
 * @package Mageplaza\DailyDeal\Plugin\Product
 */
class SetPriceItem
{
    protected $_helperData;

    /**
     * CheckUpdateQty constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(
        HelperData $helperData
    ) {
        $this->_helperData = $helperData;
    }

    /**
     * @param AbstractItem $subject
     */
    public function beforeGetCalculationPriceOriginal(AbstractItem $subject)
    {
        /** @var $item Item */
        if ($this->_helperData->isEnabled()) {
            foreach ($subject->getQuote()->getAllItems() as $item) {
                if ($item->getHasChildren()) {
                    /** get child product id configuration product **/
                    foreach ($item->getChildren() as $child) {
                        $productId = $child->getProduct()->getId();
                    }
                } else {
                    $productId = $item->getProduct()->getId();
                }
                if ($this->_helperData->checkDealProduct($productId)) {
                    $dealData = $this->_helperData->getProductDeal($productId);
                    $remainQty = $dealData->getDealQty() - $dealData->getSaleQty();
                    $qty = $item->getQty();
                    if ($qty <= $remainQty) {
                        $price = $this->_helperData->getDealPrice($productId);
                        $item->setOriginalCustomPrice($price);
                        $item->getProduct()->setIsSuperMode(true);
                    }
                } else {
                    $item->setOriginalCustomPrice(null);
                }
            }
        } else {
            foreach ($subject->getQuote()->getAllItems() as $item) {
                $item->setOriginalCustomPrice(null);
            }
        }
    }
}
