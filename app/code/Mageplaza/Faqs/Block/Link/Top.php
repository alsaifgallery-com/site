<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Faqs
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Faqs\Block\Link;

use Magento\Framework\View\Element\Html\Link;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Faqs\Helper\Data;
use Mageplaza\Faqs\Model\Config\Source\System\LinkPosition;

/**
 * Class Top
 * @package Mageplaza\Faqs\Block\Link
 */
class Top extends Link
{
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Top constructor.
     * @param Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperData,
        array $data = []
    )
    {
        $this->_helperData = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_helperData->isEnabled()
            || !$this->_helperData->getFaqsPageConfig('route')
            || strpos($this->_helperData->getFaqsPageConfig('link'), (string) LinkPosition::TOPLINK) === false
            || !$this->_helperData->isEnabledFaqsPage()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_helperData->getFaqsUrl();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return $this->_helperData->getFaqsPageConfig('title') ?: __('Faqs');
    }
}
