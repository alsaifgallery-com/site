<?php


namespace Meetanshi\AutoCancel\Block\Adminhtml\Form\Field;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Config;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class Payments extends Select
{
    protected $paymentModelConfig;
    protected $scopeConfigInterface;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfigInterface,
        Config $paymentModelConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->paymentModelConfig = $paymentModelConfig;
    }

    public function _toHtml()
    {
        $payments = $this->paymentModelConfig->getActiveMethods();
        if (!$this->getOptions()) {
            foreach ($payments as $paymentCode => $paymentModel) {
                $paymentTitle = $this->scopeConfigInterface->getValue('payment/' . $paymentCode . '/title');
                $this->addOption($paymentCode, $paymentTitle);
            }
        }
        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
