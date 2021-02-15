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

namespace Mageplaza\Faqs\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mageplaza\Faqs\Model\ArticleFactory;

/**
 * Class Article
 * @package Mageplaza\Faqs\Controller\Adminhtml
 */
abstract class Article extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mageplaza_Faqs::article';

    /**
     * Article model factory
     *
     * @var ArticleFactory
     */
    public $articleFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * Article constructor.
     * @param ArticleFactory $articleFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        ArticleFactory $articleFactory,
        Registry $coreRegistry,
        Context $context
    )
    {
        $this->articleFactory = $articleFactory;
        $this->coreRegistry   = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     * @return bool|\Mageplaza\Faqs\Model\Article
     */
    protected function initArticle($register = false)
    {
        $articleId = (int) $this->getRequest()->getParam('id');

        /** @var \Mageplaza\Faqs\Model\Article $article */
        $article = $this->articleFactory->create();

        if ($articleId) {
            $article->load($articleId);
            if (!$article->getId()) {
                $this->messageManager->addErrorMessage(__('This article no longer exists.'));

                return false;
            }
        }
        if ($register) {
            $this->coreRegistry->register('mageplaza_faqs_article', $article);
        }

        return $article;
    }
}
