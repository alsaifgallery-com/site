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

namespace Mageplaza\Faqs\Controller\Article;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\ForwardFactory;
use Mageplaza\Faqs\Helper\Data;
use Mageplaza\Faqs\Controller\AjaxSearch;

/**
 * Class Index
 * @package Mageplaza\Faqs\Controller\Article
 */
class Index extends AjaxSearch
{
    /**
     * Index constructor.
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
        parent::__construct($context, $resultPageFactory, $resultForwardFactory, $helperData);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $page = $this->_resultPageFactory->create();
        if ($this->getRequest()->isAjax()) {

            return $this->_getAjaxFilterResult();
        }
        if ($this->_helperData->isEnabled()
            && $this->_helperData->getFaqsPageConfig('route')
            && $this->_helperData->isEnabledFaqsPage()) {
            $page->getConfig()->setPageLayout($this->_helperData->getFaqsPageConfig('layout'));

            return $page;
        }

        return $this->_redirect('noroute');
    }
}
