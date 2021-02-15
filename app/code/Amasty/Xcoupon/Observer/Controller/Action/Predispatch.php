<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */


namespace Amasty\Xcoupon\Observer\Controller\Action;

use Magento\Framework\Event\Observer;

class Predispatch implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $customerSession
    ) {
        $this->request = $request;
        $this->checkoutSession = $customerSession;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $coupon = $this->request->getParam('coupon_code');

        if ($coupon) {
            if ($quote = $this->checkoutSession->getQuote()) {
                $this->checkoutSession->setCoupon($coupon);
                $quote->setCouponCode($coupon)
                    ->collectTotals()
                    ->save();
                $quote->setTotalsCollectedFlag(false);
            }
        }
    }
}
