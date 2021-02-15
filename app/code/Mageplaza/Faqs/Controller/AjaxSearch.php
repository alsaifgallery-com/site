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

namespace Mageplaza\Faqs\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\ForwardFactory;
use Mageplaza\Faqs\Helper\Data;

/**
 * Class Search
 * @package Mageplaza\Faqs\Controller
 */
abstract class AjaxSearch extends Action
{
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * Search constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Data $helperData
    )
    {
        $this->_resultPageFactory    = $resultPageFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_helperData           = $helperData;

        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    protected function _getAjaxFilterResult()
    {
        $page   = $this->_resultPageFactory->create();
        $params = $this->getRequest()->getParams();
        $layout = $page->getLayout();
        $result = [
            'faq_list' => $layout->createBlock('Mageplaza\Faqs\Block\Category\View')
                ->setTemplate('Mageplaza_Faqs::article/style/material.phtml')
                ->setIsFilter($params['filter'])->toHtml(),
            'status'   => true
        ];

        return $this->getResponse()->representJson(Data::jsonEncode($result));
    }
}
