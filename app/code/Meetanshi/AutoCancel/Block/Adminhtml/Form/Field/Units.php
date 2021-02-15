<?php


namespace Meetanshi\AutoCancel\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class Units extends Select
{
    protected $paymentMethod;

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption('0', 'Minutes');
            $this->addOption('1', 'Hours');
            $this->addOption('2', 'Days');
        }
        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
