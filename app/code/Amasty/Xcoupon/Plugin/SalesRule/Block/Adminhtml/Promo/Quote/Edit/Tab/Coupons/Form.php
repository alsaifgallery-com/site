<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Plugin\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons;

/**
 * Class Form
 * @package Amasty\Xcoupon\Plugin\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons
 * @author Artem Brunevski
 */
class Form extends \Magento\Framework\DataObject
{
    /** @var \Magento\Config\Model\Config\Source\Yesno  */
    protected $yesno;

    /**
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     */
    function __construct(
        \Magento\Config\Model\Config\Source\Yesno $yesno
    ){
        $this->yesno = $yesno;
    }

    public function aroundSetForm(
        \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Form $subject,
        \Closure $proceed,
        \Magento\Framework\Data\Form $form
    ){
        $iterator = $form->getElements()->getIterator();
        while ($iterator->valid()) {
            if (($element = $iterator->current()) &&
                $element instanceof \Magento\Framework\Data\Form\Element\Fieldset
            ) {
                $data = $element->getData();
                $data['legend'] = __('Generate Coupons');
                $element->setData($data);
                $element->addField(
                    'amasty_xcoupon_delete_existing_coupons',
                    'select',
                    [
                        'name' => 'amasty_xcoupon_delete_existing_coupons',
                        'label' => __('Delete Existing Coupons'),
                        'title' => __('Delete Existing Coupons'),
                        'values' => $this->yesno->toOptionArray()
                    ],
                    'qty'
                );
                $iterator->next();
            }

            /** @var \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Form $result */
            $result = $proceed($form);

            return $result;
        }
    }
}