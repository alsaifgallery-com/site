<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\Faqs\Block\Adminhtml\Article\Edit\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mageplaza\Faqs\Model\ArticleFactory;

/**
 * Class Statistic
 * @package Mageplaza\Faqs\Block\Adminhtml\Article\Edit\Tab
 */
class Statistic extends Template
{
    /**
     * Link to template
     *
     * @var string
     */
    protected $_template = 'article/statistic.phtml';

    /**
     * @var \Mageplaza\Faqs\Model\ArticleFactory
     */
    protected $_articleFactory;

    /**
     * Statistic constructor.
     *
     * @param Context $context
     * @param ArticleFactory $articleFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArticleFactory $articleFactory,
        array $data = []
    )
    {
        $this->_articleFactory = $articleFactory;

        parent::__construct($context, $data);
    }

    /**
     * Get current Article
     *
     * @return \Mageplaza\Faqs\Model\Article
     */
    protected function _getArticle()
    {
        $id      = (int) $this->getRequest()->getParam('id');
        $article = $this->_articleFactory->create()->load($id);
        return $article;
    }

    /**
     * Get current article view
     *
     * @return mixed
     */
    public function getView()
    {
        return (int) $this->_getArticle()->getViews();
    }

    /**
     * Get current article positive
     *
     * @return mixed
     */
    public function getPositive()
    {
        if ($this->getActionNum() == 0) {
            return 0;
        }
        $positiveRate = round((int) $this->_getArticle()->getPositives() / $this->getActionNum(), 2) * 100;
        return $positiveRate;
    }

    /**
     * Get current article negative
     *
     * @return mixed
     */
    public function getNegative()
    {
        if ($this->getActionNum() == 0) {
            return 0;
        }
        $negativeRate = round((int) $this->_getArticle()->getNegatives() / $this->getActionNum(), 2) * 100;
        return $negativeRate;
    }

    /**
     * Get current article action
     *
     * @return mixed
     */
    public function getActionNum()
    {
        $positiveNum = (int) $this->_getArticle()->getPositives();
        $negativeNum = (int) $this->_getArticle()->getNegatives();
        $actionNum   = $positiveNum + $negativeNum;
        return $actionNum;
    }
}
