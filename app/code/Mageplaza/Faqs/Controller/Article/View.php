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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Faqs\Helper\Data;

/**
 * Class View
 * @package Mageplaza\Blog\Controller\Category
 */
class View extends Action
{
    const ACTION_POSITIVE = 'positive';
    const ACTION_NEGATIVE = 'negative';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * @type \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * View constructor.
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
        parent::__construct($context);

        $this->resultPageFactory     = $resultPageFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_helperData           = $helperData;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Exception
     */
    public function execute()
    {
        $id      = $this->getRequest()->getParam('id');
        $article = $this->_helperData->getFactoryByType(Data::TYPE_ARTICLE)->create()->load($id);
        if ($this->getRequest()->isAjax()) {
            $actionType = $this->getRequest()->getParam('action');
            if ($actionType == self::ACTION_POSITIVE) {
                $article->setPositives($article->getPositives() + 1)->save();
            }
            elseif ($actionType == self::ACTION_NEGATIVE) {
                $article->setNegatives($article->getNegatives() + 1)->save();
            }
            $result = [
                'status'        => true,
                'like_count'    => $article->getPositives(),
                'dislike_count' => $article->getNegatives(),
            ];

            return $this->getResponse()->representJson(Data::jsonEncode($result));
        }

        if ($article->getVisibility() == 1 && $this->_helperData->isEnabledDetailPage()) {
            $article->setViews($article->getViews() + 1)->save();
            $page = $this->resultPageFactory->create();
            $page->getConfig()->setPageLayout($this->_helperData->getConfigGeneral('question_detail_page/layout'));
            return $page;
        }

        return $this->_resultForwardFactory->create()->forward('noroute');
    }
}
