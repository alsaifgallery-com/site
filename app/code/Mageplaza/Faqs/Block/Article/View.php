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

namespace Mageplaza\Faqs\Block\Article;

use Mageplaza\Faqs\Helper\Data;
use Mageplaza\Faqs\Model\Article;

/**
 * Class View
 * @package Mageplaza\Faqs\Block\Article
 * @method Article getArticle()
 * @method void setArticle($article)
 */
class View extends ArticleList
{
    /**
     * @var string
     */
    protected $_article;

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $article = $this->_getFaqsObject();

            if ($article) {
                $breadcrumbs->addCrumb($article->getUrlKey(), [
                        'label' => $article->getName(),
                        'title' => $article->getName()
                    ]
                );
            }
        }
    }

    /**
     * @return mixed|string
     */
    protected function _getFaqsObject()
    {
        if (!$this->_article) {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $article = $this->helperData->getObjectByParam($id, null, Data::TYPE_ARTICLE);
                if ($article && $article->getId()) {
                    $this->_article = $article;
                    $this->setArticle($article);
                }
            }
        }

        return $this->_article;
    }

    /**
     * Get article detail page title
     *
     * @param bool $meta
     * @return array
     */
    public function getFaqsTitle($meta = false)
    {
        $faqsTitle = parent::getFaqsTitle($meta);
        $article   = $this->_getFaqsObject();
        if (!$article) {
            return $faqsTitle;
        }
        if ($meta) {
            if ($article->getMetaTitle()) {
                array_push($faqsTitle, $article->getMetaTitle());
            }
            else {
                array_push($faqsTitle, ucfirst($article->getName()));
            }

            return $faqsTitle;
        }

        return ucfirst($article->getName());
    }

    /**
     * Get category list by article on html format
     *
     * @param $article
     * @return null|string
     */
    public function getCategoriesByArticleHtml($article)
    {
        if (!$article->getCategoryIds()) {
            return null;
        }
        $categories   = $this->helperData->getCategoriesByArticle($article->getCategoryIds());
        $categoryHtml = [];
        foreach ($categories as $_cat) {
            $categoryHtml[] = '<a class="mp-info" href="' . $this->helperData->getFaqsUrl($_cat, Data::TYPE_CATEGORY) . '">' . $_cat->getName() . '</a>';
        }
        $result = implode(', ', $categoryHtml);

        return $result;
    }

    /**
     * Get customer group ID
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        $customerGroup = '0';
        if ($this->helperData->isLoggedIn()) {
            $customerGroup = $this->_customerSession->create()->getCustomer()->getGroupId();
        }

        return $customerGroup;
    }

    /**
     * Check conditions to show helpful
     *
     * @return bool
     */
    public function isShowHelpful()
    {
        $helpfulConfig   = $this->helperData->getConfigGeneral('rating_restrict');
        $isEnableHelpful = $this->helperData->getConfigGeneral('is_show_helpful');

        if (!$isEnableHelpful) {
            return false;
        }

        return strpos($helpfulConfig, $this->getCustomerGroupId()) !== false;
    }
}
