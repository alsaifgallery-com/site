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

namespace Mageplaza\Faqs\Controller\Category;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Faqs\Helper\Data;
use Mageplaza\Faqs\Controller\AjaxSearch;

/**
 * Class View
 * @package Mageplaza\Faqs\Controller\Category
 */
class View extends AjaxSearch
{
    /**
     * View constructor.
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
        $id       = $this->getRequest()->getParam('id');
        $category = $this->_helperData->getFactoryByType(Data::TYPE_CATEGORY)->create()->load($id);

        if ($this->getRequest()->isAjax()) {
            return $this->_getAjaxFilterResult();
        }

        if ($category->getEnabled()) {
            return $this->_resultPageFactory->create();
        }

        return $this->_resultForwardFactory->create()->forward('noroute');
    }
}
