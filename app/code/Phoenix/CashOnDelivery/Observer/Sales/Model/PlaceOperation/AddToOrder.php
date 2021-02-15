<?php
/**
 * Phoenix Cash on Delivery module for Magento 2
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mage
 * @package    Phoenix_CashOnDelivery
 * @copyright  Copyright (c) 2017 Phoenix Media GmbH (http://www.phoenix-media.eu)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\CashOnDelivery\Observer\Sales\Model\PlaceOperation;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;

class AddToOrder implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        $orderState = Order::STATE_NEW;
        $order->setState($orderState)->setStatus('pending');

        return $order;
    }
}
