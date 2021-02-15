<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_Popup
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Popup\Block\Adminhtml\Popup;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize Popup edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'popup_id';
        $this->_blockGroup = 'Bss_Popup';
        $this->_controller = 'adminhtml_popup';
        parent::_construct();
        $this->buttonList->update('delete', 'label', __('Delete'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('save', 'label', __('Save Pop-up'));
    }

    /**
     * Retrieve text for header element depending on loaded Popup
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Bss\Popup\Model\Popup $Popup */
        $popup = $this->coreRegistry->registry('bss_popup_popup');
        if ($popup->getId()) {
            return __("");
        }
        return __('New Pop-up');
    }
}
