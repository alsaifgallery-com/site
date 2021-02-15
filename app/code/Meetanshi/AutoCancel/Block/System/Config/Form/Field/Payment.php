<?php


namespace Meetanshi\AutoCancel\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Payment extends AbstractFieldArray
{
    protected $columns = [];
    protected $paymentMethodRenderer;
    protected $unitsRenderer;
    protected $_addAfter = true;
    protected $addButtonLabel;

    public function renderCellTemplate($columnName)
    {
        if ($columnName == "active") {
            $this->columns[$columnName]['class'] = 'input-text required-entry validate-number';
        }
        return parent::renderCellTemplate($columnName);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->addButtonLabel = __('Add');
    }

    protected function _prepareToRender()
    {
        $this->addColumn(
            'method',
            [
                'label' => __('Payment Method'),
                'class' => 'required-entry',
                'renderer' => $this->getPaymentMethodsRenderer(),
            ]
        );
        $this->addColumn(
            'days',
            [
                'label' => __('Duration'),
                'size' => '15px',
                'class' => 'required-entry validate-digits'
            ]
        );

        $this->addColumn(
            'units',
            [
                'label' => __('Units'),
                'size' => '5px',
                'renderer' => $this->getUnitsRenderer(),
            ]
        );

        $this->_addAfter = false;
        $this->addButtonLabel = __('Add Payment');
    }

    protected function getPaymentMethodsRenderer()
    {
        if (!$this->paymentMethodRenderer) {
            $this->paymentMethodRenderer = $this->getLayout()->createBlock(
                '\Meetanshi\AutoCancel\Block\Adminhtml\Form\Field\Payments',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->paymentMethodRenderer;
    }

    protected function getUnitsRenderer()
    {
        if (!$this->unitsRenderer) {
            $this->unitsRenderer = $this->getLayout()->createBlock(
                '\Meetanshi\AutoCancel\Block\Adminhtml\Form\Field\Units',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->unitsRenderer;
    }

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $paymentMethod = $row->getMethod();
        $units = $row->getUnits();
        $options = [];
        if ($paymentMethod) {
            $options['option_' . $this->getPaymentMethodsRenderer()->calcOptionHash($paymentMethod)] = 'selected="selected"';
        }
        if ($units) {
            $options['option_' . $this->getUnitsRenderer()->calcOptionHash($units)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
