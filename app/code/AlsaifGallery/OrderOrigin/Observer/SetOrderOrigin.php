<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SetOrderOrigin
 *
 * @author nada
 */
namespace AlsaifGallery\OrderOrigin\Observer;

class SetOrderOrigin implements \Magento\Framework\Event\ObserverInterface {
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
//        if ('some logic') {
//            $order->setOrderOrigin("test");
//        }
        return $this;
    }
}
