<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Observer\Sales\Order\Payment;

use Amasty\CashOnDelivery\Model\ConfigProvider;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\Sales\Model\Order;

class Place implements ObserverInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getPayment()->getOrder();

        if ($order->getPayment()->getMethod() === Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE
            && $status = $this->configProvider->getOrderStatus()
        ) {
            $state = $status;

            if ($status == 'pending') {
                $state = Order::STATE_NEW;
            }
            
            $order->setState($state)
                ->setStatus($status);

            foreach ($order->getStatusHistories() as $item) {
                $item->setStatus($status);
            }
        }
    }
}
