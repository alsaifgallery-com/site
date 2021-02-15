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

use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Faqs\Helper\Data;
use Mageplaza\Faqs\Model\Config\Source\System\QuestionStyle;

/**
 * Class Product
 * @package Mageplaza\Faqs\Block\Article
 */
class Product extends Template
{
    /**
     * @var Data
     */
    public $helperData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Mageplaza\Faqs\Model\ResourceModel\Article\Collection
     */
    protected $_relatedArticles;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * Product constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $dateTime
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTime $dateTime,
        Data $helperData,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->_date         = $dateTime;
        $this->helperData    = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        $this->setTabTitle();

        parent::_construct();
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->_coreRegistry->registry('product');

        return $product ? $product->getId() : null;
    }

    /**
     * @return \Mageplaza\Faqs\Model\ResourceModel\Article\Collection
     */
    public function getArticleByProduct()
    {
        if (!$this->_relatedArticles) {
            /** @var \Mageplaza\Faqs\Model\ResourceModel\Article\Collection $collection */
            $collection = $this->helperData->getArticleCollection();
            $collection->getSelect()
                ->join([
                    'mfap' => $collection->getTable('mageplaza_faqs_article_product')],
                    'mfap.article_id = main_table.article_id AND mfap.entity_id = ' . $this->getProductId()
                );

            $this->_relatedArticles = $collection;
        }

        return $this->_relatedArticles;
    }

    /**
     * Get question display type
     *
     * @return mixed
     */
    public function isCollapsible()
    {
        return $this->helperData->getProductTabConfig('question_style') == QuestionStyle::COLLAPSIBLE;
    }

    /**
     * Get limit question per category
     *
     * @return int
     */
    public function getLimit()
    {
        return ($this->helperData->getProductTabConfig('limit_question')) ?: 5;
    }

    /**
     * @return string
     */
    public function getCurrentDate()
    {
        return $this->_date->date();
    }

    /**
     * @param $date
     * @return string
     */
    public function getTimeAgo($date)
    {
        $strTime = ["second", "minute", "hour", "day", "month", "year"];
        $length  = ["60", "60", "24", "30", "12", "10"];

        $timestamp   = strtotime($date);
        $currentTime = strtotime($this->getCurrentDate());
        if ($currentTime < $timestamp) {
            return 'Just now';
        }

        $diff = $currentTime - $timestamp;
        for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
            $diff = $diff / $length[$i];
        }
        $diff = round($diff);

        return ($diff == 1) ? $diff . " " . $strTime[$i] . " ago " : $diff . " " . $strTime[$i] . "s ago ";
    }

    /**
     * @return mixed
     */
    public function isShowName()
    {
        return $this->helperData->getProductTabConfig('show_name');
    }

    /**
     * @return mixed
     */
    public function isShowDate()
    {
        return $this->helperData->getProductTabConfig('show_date');
    }

    /**
     * Set tab title
     *
     */
    public function setTabTitle()
    {
        $title = ($this->helperData->getProductTabConfig('title')) ?: __('FAQs');
        $this->setTitle($title);
    }
}
