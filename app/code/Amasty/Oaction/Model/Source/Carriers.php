<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */


namespace Amasty\Oaction\Model\Source;

class Carriers implements \Magento\Framework\Option\ArrayInterface
{
    private $shippingConfig;

    public function __construct(
        \Magento\Shipping\Model\Config $shippingConfig
    ) {
        $this->shippingConfig = $shippingConfig;
    }

    public function toOptionArray()
    {
        $options = [
            [
                'value' => '',
                'label' => '----'
            ],
            [
                'value' => 'custom',
                'label' => __('Custom')
            ]
        ];

        foreach ($this->shippingConfig->getAllCarriers() as $k => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $options[] = [
                    'value' => $k,
                    'label' => $carrier->getConfigData('title'),
                ];
            }
        }

        return $options;
    }
}
