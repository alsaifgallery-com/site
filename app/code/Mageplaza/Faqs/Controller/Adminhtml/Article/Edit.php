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

namespace Mageplaza\Faqs\Controller\Adminhtml\Article;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Faqs\Controller\Adminhtml\Article;
use Mageplaza\Faqs\Model\ArticleFactory;

/**
 * Class Edit
 * @package Mageplaza\Faqs\Controller\Adminhtml\Article
 */
class Edit extends Article
{
    /**
     * Page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * Edit constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ArticleFactory $articleFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ArticleFactory $articleFactory,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($articleFactory, $registry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Mageplaza\Faqs\Model\Article $article */
        $article = $this->initArticle();

        if (!$article) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('mageplaza_faqs_article_data', true);
        if (!empty($data)) {
            $article->setData($data);
        }

        $this->coreRegistry->register('mageplaza_faqs_article', $article);

        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_Faqs::article');
        $resultPage->getConfig()->getTitle()->set(__('Manage Articles'));

        $title = $article->getId() ? __('Edit "%1"', $article->getName()) : __('New Article/Question');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
