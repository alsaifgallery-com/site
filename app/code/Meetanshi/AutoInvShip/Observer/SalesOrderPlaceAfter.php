<?php

namespace Meetanshi\AutoInvShip\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Service\InvoiceService;
use Meetanshi\AutoInvShip\Helper\Data as HelperData;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Invoice;
use Magento\Shipping\Model\ShipmentNotifier;
use Magento\Sales\Model\Convert\OrderFactory;

class SalesOrderPlaceAfter implements ObserverInterface
{
    protected $helper;
    protected $invoiceSender;
    protected $invoiceService;
    protected $transectionFactory;
    protected $shipmentNotifier;
    protected $orderFactory;

    public function __construct(HelperData $helper, InvoiceSender $invoiceSender, InvoiceService $invoiceService, TransactionFactory $transactionFactory, ShipmentNotifier $shipmentNotifier, OrderFactory $orderFactory)
    {
        $this->helper = $helper;
        $this->invoiceSender = $invoiceSender;
        $this->invoiceService = $invoiceService;
        $this->transectionFactory = $transactionFactory;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->orderFactory = $orderFactory;
    }

    public function execute(Observer $observer)
    {
        if ($this->helper->isEnabled()) {
            $order = $observer->getEvent()->getOrder()->save();
            if (!$order->getId()) {
                throw new LocalizedException(__('Order is not found.'));
            }
            $code = $order->getPayment()->getMethodInstance()->getCode();
            if ($this->helper->checkPayment($code)) {
                if ($this->helper->autoInvoice()) {
                    if ($order->getState() == Order::STATE_NEW) {
                        try {
                            if (!$order->canInvoice()) {
                                $order->addStatusHistoryComment('Sorry, Order cannot be invoiced.', false)->save();
                            }
                            $invoice = $this->invoiceService->prepareInvoice($order);
                            if (!$invoice) {
                                $order->addStatusHistoryComment('Can\'t generate the invoice right now.', false)->save();
                            }

                            if (!$invoice->getTotalQty()) {
                                $order->addStatusHistoryComment('Can\'t generate an invoice without products.', false)->save();
                            }
                            $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);
                            $invoice->register();
                            $invoice->getOrder()->setCustomerNoteNotify(false);
                            $invoice->getOrder()->setIsInProcess(true);
                            $transactionSave = $this->transectionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
                            $transactionSave->save();
                            try {
                                $this->invoiceSender->send($invoice);
                            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                                $order->addStatusHistoryComment('Can\'t send the invoice Email right now.', false)->save();
                            }
                            $shipment = false;
                            if ($this->helper->autoShipment()) {
                                $shipment = $this->orderFactory->create()->toShipment($order);
                                foreach ($order->getAllItems() as $orderItem) {
                                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                                        continue;
                                    }
                                    $qtyShipped = $orderItem->getQtyToShip();
                                    $shipmentItem = $this->orderFactory->create()->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                                    $shipment->addItem($shipmentItem);
                                }
                                $shipment->register();
                                $shipment->getOrder()->setIsInProcess(true);

                                try {
                                    $shipment->save();
                                    $shipment->getOrder()->save();
                                    $this->shipmentNotifier->notify($shipment);
                                    $shipment->save();
                                } catch (\Exception $e) {
                                    throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
                                }
                            }
                            if ($shipment) {
                                $order->addStatusHistoryComment('Automatically Invoice and Shipment Generated.', false)->save();
                            } else {
                                $order->addStatusHistoryComment('Automatically Invoice Generated.', false)->save();
                            }
                        } catch (LocalizedException $e) {
                            $order->addStatusHistoryComment('Error occurred during automatically Invoice and Shipment generation. Exception message: ' . $e->getMessage(), false)->save();
                        }
                    }
                }
            }
        }
    }
}
