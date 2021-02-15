<?php

namespace Meetanshi\AutoInvShip\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    public function __construct(Context $context)
    {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue('autoinvship/general/active', ScopeInterface::SCOPE_STORE);
    }

    public function autoInvoice()
    {
        return $this->scopeConfig->getValue('autoinvship/general/auto_invoice', ScopeInterface::SCOPE_STORE);
    }

    public function checkPayment($code)
    {
        $paymentMethods = $this->scopeConfig->getValue('autoinvship/general/payment_methods', ScopeInterface::SCOPE_STORE);
        if (in_array($code, explode(',', $paymentMethods))) {
            return true;
        }
        return false;
    }

    public function autoShipment()
    {
        return $this->scopeConfig->getValue('autoinvship/general/auto_shipment', ScopeInterface::SCOPE_STORE);
    }
}
