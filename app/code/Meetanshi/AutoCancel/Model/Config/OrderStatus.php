<?php

namespace Meetanshi\AutoCancel\Model\Config;

use Magento\Sales\Model\Config\Source\Order\Status;
use Magento\Framework\Option\ArrayInterface;;

class OrderStatus implements ArrayInterface
{
    protected $orderStatus;

    public function __construct(Status $status)
    {
        $this->orderStatus = $status;
    }

    public function toOptionArray()
    {
        return [
            ['value' => 'pending', 'label' => __('Pending')],
            ['value' => 'pending_payment', 'label' => __('Pending Payment')],
            ['value' => 'fraud', 'label' => __('Suspected Fraud')],
            ['value' => 'payment_review', 'label' => __('Payment Review')],
            ['value' => 'holded', 'label' => __('On Hold')]
        ];
    }
}