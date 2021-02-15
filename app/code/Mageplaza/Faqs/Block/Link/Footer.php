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

/**
 * Class Footer
 * @package Mageplaza\Faqs\Block\Html
 */
class Footer extends Link
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_Faqs::link/footer.phtml';

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Footer constructor.
     *
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
    public function getHref()
    {
        return $this->_helperData->getFaqsUrl('');
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_helperData->getFaqsPageConfig('title') ?: __('Faqs');
    }

    /**
     * Check if link leads to URL equivalent to URL of currently displayed page
     *
     * @return bool
     */
    public function isCurrent()
    {
        return $this->getRequest()->getFullActionName() == 'mpfaqs_article_index';
    }
}
