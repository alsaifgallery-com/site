<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons;

/**
 * Class Import
 * @package Amasty\Xcoupon\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons
 * @author Artem Brunevski
 */
class Import extends \Magento\Backend\Block\Widget\Form\Generic
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

        $fieldset = $form->addFieldset('amasty_import_fieldset', [
            'legend' => 'Import Coupons',
            'class' => 'amasty-xcoupon-expand-fieldset'
        ]);

        $fieldset->addField(
            'amasty_xcoupon_delete_existing_coupons',
            'select',
            [
                'name' => 'amasty_xcoupon_delete_existing_coupons',
                'label' => __('Delete Existing Coupons'),
                'title' => __('Delete Existing Coupons'),
                'values' => $this->yesno->toOptionArray()
            ]
        );

        $fieldset->addField('amasty_xcoupon_file', 'file', [
            'label' => __('CSV File'),
            'name' => 'amasty_xcoupon_file',
            'style' => 'padding: 0',
            'note' => __('CSV File Structure: Coupon Code *, Created, Uses, Times Used, Uses per Customer, Uses per Coupon, Expiration date <br/> Each coupon code on a new line <br/>* - mandatory')
        ]);

        $gridBlock = $this->getLayout()->getBlock('promo_quote_edit_tab_coupons_grid');
        $gridBlockJsObject = '';
        if ($gridBlock) {
            $gridBlockJsObject = $gridBlock->getJsObjectName();
        }

        $fieldset->addField(
            'amasty_xcoupon_import_button',
            'note',
            [
                'text' => $this->getButtonHtml(
                    __('Import'),
                    "amastyXcouponImport({$ruleId}, '{$this->getImportUrl()}', '{$gridBlockJsObject}')",
                    'generate'
                )
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getImportUrl()
    {
        return $this->getUrl('amasty_xcoupon/import/run');
    }
}