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

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\SessionFactory;
use Mageplaza\Faqs\Helper\Data;
use Mageplaza\Faqs\Model\Config\Source\System\QuestionStyle;

/**
 * Class ArticleList
 * @package Mageplaza\Faqs\Block\Article
 */
class ArticleList extends Template
{
    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var SessionFactory
     */
    protected $_customerSession;

    /**
     * ArticleList constructor.
     *
     * @param Context $context
     * @param SessionFactory $customerSession
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionFactory $customerSession,
        Data $helperData,
        array $data = []
    )
    {
        $this->_customerSession = $customerSession;
        $this->helperData       = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->_storeManager->getStore()->getBaseUrl()
            ])
                ->addCrumb($this->helperData->getRoute(), $this->getBreadcrumbsData());
        }

        $this->applySeoCode();

        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    protected function getBreadcrumbsData()
    {
        $label = $this->helperData->getFaqsName();

        $data = [
            'label' => $label,
            'title' => $label
        ];

        if ($this->getRequest()->getFullActionName() != 'mpfaqs_article_index') {
            $data['link'] = $this->helperData->getFaqsUrl();
        }

        return $data;
    }

    /**
     * @return $this
     */
    public function applySeoCode()
    {
        $this->pageConfig->getTitle()->set(join($this->getTitleSeparator(), array_reverse($this->getFaqsTitle(true))));

        $object = $this->_getFaqsObject();

        $description = $object ? $object->getMetaDescription() : $this->helperData->getFaqsPageConfig('seo/meta_description');
        $this->pageConfig->setDescription($description);

        $keywords = $object ? $object->getMetaKeywords() : $this->helperData->getFaqsPageConfig('seo/meta_keyword');
        $this->pageConfig->setKeywords($keywords);

        $robots = $object ? $object->getMetaRobots() : $this->helperData->getFaqsPageConfig('seo/meta_robot');
        $this->pageConfig->setRobots($robots);

        return $this;
    }

    /**
     * @return null
     */
    protected function _getFaqsObject()
    {
        return null;
    }

    /**
     * Retrieve HTML title value separator (with space)
     *
     * @return string
     */
    public function getTitleSeparator()
    {
        $separator = (string) $this->helperData->getConfigValue('catalog/seo/title_separator');

        return ' ' . $separator . ' ';
    }

    /**
     * @param bool $meta
     * @return array
     */
    public function getFaqsTitle($meta = false)
    {
        $pageTitle = $this->helperData->getFaqsPageConfig('title') ?: __('Frequently Answer and Question');
        if ($meta) {
            $title = $this->helperData->getFaqsPageConfig('seo/meta_title') ?: $pageTitle;

            return [$title];
        }

        return $pageTitle;
    }

    /**
     * Get category collection
     *
     * @return \Mageplaza\Faqs\Model\ResourceModel\Category\Collection
     */
    public function getCategoryCollection()
    {
        return $this->helperData->getCategoryCollection();
    }

    /**
     * @param $catId
     * @param null $where
     * @return \Mageplaza\Faqs\Model\ResourceModel\Article\Collection
     */
    public function getArticleByCategory($catId, $where = null)
    {
        return $this->helperData->getArticleByCategory($catId, $where);
    }

    /**
     * Get num.of category column
     *
     * @return int
     */
    public function getCategoryColumns()
    {
        return (int) $this->helperData->getFaqsPageConfig('category_column');
    }

    /**
     * Get question display type
     *
     * @return mixed
     */
    public function isCollapsible()
    {
        return $this->helperData->getFaqsPageConfig('question_style') == QuestionStyle::COLLAPSIBLE;
    }


    /**
     * @param $date
     * @param $dateType
     * @return string
     */
    public function getDateFormat($date, $dateType)
    {
        return $this->helperData->getDateFormat($date, $dateType);
    }

    /**
     * @param $priority
     * @param $message
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMessagesHtml($priority, $message)
    {
        /** @var $messagesBlock \Magento\Framework\View\Element\Messages */
        $messagesBlock = $this->_layout->createBlock(\Magento\Framework\View\Element\Messages::class);
        $messagesBlock->{$priority}(__($message));

        return $messagesBlock->toHtml();
    }
}