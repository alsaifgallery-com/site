<?php

namespace Meetanshi\AutoCancel\Cron;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Meetanshi\AutoCancel\Helper\Data;
use Magento\Framework\App\ObjectManager;

class AutoCancel
{
    private $orderCollectionFactory;
    private $helper;

    public function __construct(
        Data $helper,
        CollectionFactory $orderCollectionFactory
    )
    {
        $this->helper = $helper;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    public function execute()
    {
        if ($this->helper->getAutoCancel()) :
            try {
                $paymentData = json_decode($this->helper->getPaymentConfig());
                $afterDate = $this->helper->getAfterDate();
                $orderStatus = explode(',', $this->helper->getStatusConfig());
                $collection = $this->orderCollectionFactory->create()
                    ->addAttributeToFilter('status', ['in' => $orderStatus])
                    ->addAttributeToFilter('created_at', ['gt' => $afterDate.' 00:00:00']);

                $cancelOrder = '';
                if ($collection->count()) :
                    foreach ($paymentData as $key => $value) :
                        $currentData = $this->helper->getCurrentTime();
                        /** @var \Magento\Sales\Model\Order $order */
                        foreach ($collection as $order) {
                            try {
                                $customerEmail = '';
                                $customerName = '';
                                if ($value->method == $order->getPayment()->getMethod()) {
                                    $orderDate = $order->getCreatedAt();
                                    if ($value->units == 2) {
                                        $currentDate = date_create($currentData);
                                        $orderDate = date_create($orderDate);
                                        $dateDiffernt = date_diff($orderDate, $currentDate);
                                        $dayDifferent = $dateDiffernt->format("%a");
                                        if ($dayDifferent >= $value->days) {
                                            $order->cancel();
                                            $order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED);
                                            //$order->addStatusHistoryComment('Order was set to "Cancelled" due to the pending payment.');
                                            $history = $order->addStatusHistoryComment('Order was set to "Cancelled" due to the pending payment.', 'canceled');
                                            $history->setIsCustomerNotified(1);
                                            $history->save();
                                            $order->save();
                                            $cancelOrder = $order->getIncrementId() . ' ' . $cancelOrder;
                                            $customerEmail = $order->getCustomerEmail();

                                            if ($order->getCustomerFirstname() == '') {
                                                $firstName = $order->getBillingAddress()->getFirstname();
                                                $customerName = $firstName . ' ' . $order->getBillingAddress()->getLastname();
                                            } else {
                                                $customerName = $order->getCustomerName();
                                            }
                                        }
                                    } elseif ($value->units == 1) {
                                        $hourdiff = round((strtotime($currentData) - strtotime($orderDate)) / 3600, 0);
                                        if ($hourdiff >= $value->days) {
                                            $order->cancel();
                                            $order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED);
                                            //$order->addStatusHistoryComment('Order was set to "Cancelled" due to the pending payment.');

                                            $history = $order->addStatusHistoryComment('Order was set to "Cancelled" due to the pending payment.', 'canceled');
                                            $history->setIsCustomerNotified(1);
                                            $history->save();

                                            $order->save();
                                            $cancelOrder = $order->getIncrementId() . ' ' . $cancelOrder;
                                            $customerEmail = $order->getCustomerEmail();

                                            if ($order->getCustomerFirstname() == '') {
                                                $firstName = $order->getBillingAddress()->getFirstname();
                                                $customerName = $firstName . ' ' . $order->getBillingAddress()->getLastname();
                                            } else {
                                                $customerName = $order->getCustomerName();
                                            }
                                        }
                                    } else {
                                        $minutesdiff = round((strtotime($currentData) - strtotime($orderDate)) / 60, 0);
                                        if ($minutesdiff >= $value->days) {
                                            $order->cancel();
                                            $order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED);
                                            //$order->addStatusHistoryComment('Order was set to "Cancelled" due to the pending payment.');
                                            $history = $order->addStatusHistoryComment('Order was set to "Cancelled" due to the pending payment.', 'canceled');
                                            $history->setIsCustomerNotified(1);
                                            $history->save();
                                            $order->save();

                                            $cancelOrder = $order->getIncrementId() . ' ' . $cancelOrder;
                                            $customerEmail = $order->getCustomerEmail();

                                            if ($order->getCustomerFirstname() == '') {
                                                $firstName = $order->getBillingAddress()->getFirstname();
                                                $customerName = $firstName . ' ' . $order->getBillingAddress()->getLastname();
                                            } else {
                                                $customerName = $order->getCustomerName();
                                            }
                                        }
                                    }
                                }
                                if (!empty($cancelOrder) && $this->helper->isSendCustomerEmail()) {
                                    $this->helper->sendCustomerMail($order->getIncrementId(), $customerEmail, $customerName);
                                }
                            } catch(\Exception $e){
                                ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
                            }
                        }
                    endforeach;
                    if (!empty($cancelOrder) && $this->helper->isSendAdminEmail()) {
                        $this->helper->sendCustomMailSendMethod($cancelOrder);
                    }
                endif;
            } catch (\Exception $e) {
                ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
            }
        endif;

        return $this;
    }
}