<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons;

/**
 * Class Generate
 * @package Amasty\Xcoupon\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons
 * @author Artem Brunevski
 */
class Generate extends \Magento\Backend\Block\Widget\Form\Generic
{
    /** @var \Magento\Config\Model\Config\Source\Yesno  */
    protected $yesno;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = []
    )
    {
        $this->yesno = $yesno;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /*
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry(\Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE);
        $ruleId = $model->getId();

        if (!$ruleId) {
            return parent::_prepareForm();
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('amasty_generate_fieldset', [
            'legend' => 'Generate Coupons by Template',
            'class' => 'amasty-xcoupon-expand-fieldset'
        ]);

        $fieldset->addField(
            'amasty_xcoupon_qty',
            'text',
            [
                'name' => 'qty',
                'label' => __('Coupon Qty'),
                'title' => __('Coupon Qty'),
                'required' => true,
                'class' => 'validate-digits validate-greater-than-zero'
            ]
        );

        $fieldset->addField(
            'amasty_xcoupon_delete_existing_coupons',
            'select',
            [
                'name' => 'clean',
                'label' => __('Delete Existing Coupons'),
                'title' => __('Delete Existing Coupons'),
                'values' => $this->yesno->toOptionArray()
            ]
        );

        $fieldset->addField('amasty_xcoupon_generate_pattern', 'text', [
            'label' => __('Template'),
            'required' => true,
            'name' => 'pattern',
            'note' => __('<b>L</b> - letter, <b>D</b> - digit<br/>e.g. PROMO_LLDDD results in PROMO_DF627')
        ]);

        $gridBlock = $this->getLayout()->getBlock('promo_quote_edit_tab_coupons_grid');
        $gridBlockJsObject = '';
        if ($gridBlock) {
            $gridBlockJsObject = $gridBlock->getJsObjectName();
        }
        $idPrefix = $form->getHtmlIdPrefix();

        $fieldset->addField(
            'amasty_xcoupon_generate_button',
            'note',
            [
                'text' => $this->getButtonHtml(
                    __('Generate'),
                    "amastyXcouponGenerate({$ruleId}, '{$this->getGenerateUrl()}', '{$gridBlockJsObject}')",
                    'generate'
                )
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getGenerateUrl()
    {
        return $this->getUrl('amasty_xcoupon/generate/run');
    }
}