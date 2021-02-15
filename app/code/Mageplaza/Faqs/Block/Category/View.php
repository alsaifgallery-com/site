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

namespace Mageplaza\Faqs\Block\Category;

use Mageplaza\Faqs\Helper\Data;
use Mageplaza\Faqs\Block\Article\ArticleList;
use Mageplaza\Faqs\Model\Category;

/**
 * Class View
 *
 * @package Mageplaza\Faqs\Block\Category
 * @method Category getCategory()
 * @method void setCategory($category)
 */
class View extends ArticleList
{
    /**
     * @var string
     */
    protected $_category;

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $category = $this->_getFaqsObject();

            if ($category) {
                $breadcrumbs->addCrumb($category->getUrlKey(), [
                        'label' => $category->getName(),
                        'title' => $category->getName()
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
        if (!$this->_category) {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $category = $this->helperData->getObjectByParam($id, null, Data::TYPE_CATEGORY);
                if ($category && $category->getId()) {
                    $this->_category = $category;
                    $this->setCategory($category);
                }
            }
        }

        return $this->_category;
    }

    /**
     * Get category detail page title
     *
     * @param bool $meta
     * @return array
     */
    public function getFaqsTitle($meta = false)
    {
        $faqsTitle = parent::getFaqsTitle($meta);
        $category  = $this->_getFaqsObject();
        if (!$category) {
            return $faqsTitle;
        }
        if ($meta) {
            if ($category->getMetaTitle()) {
                array_push($faqsTitle, $category->getMetaTitle());
            }
            else {
                array_push($faqsTitle, ucfirst($category->getName()));
            }

            return $faqsTitle;
        }

        return ucfirst($category->getName());
    }

    /**
     * Get limit question per category
     *
     * @return int
     */
    public function getLimit()
    {
        return ($this->helperData->getFaqsPageConfig('limit_question')) ?: 5;
    }

    /**
     * @return \Mageplaza\Faqs\Model\ResourceModel\Category\Collection
     */
    public function getCategoryCollection()
    {
        $collection = parent::getCategoryCollection();

        if ($this->getIsFilter()) {
            $where = "AND `mpfa`.`name` LIKE '%" . $this->getIsFilter() . "%'";
            return $collection->getQuestionNum($where);
        }

        return $collection->getQuestionNum();
    }

    /**
     * @param $catId
     * @param null $where
     * @return \Mageplaza\Faqs\Model\ResourceModel\Article\Collection
     */
    public function getArticleByCategory($catId, $where = null)
    {
        if ($this->getIsFilter()) {
            $where = "AND `main_table`.`name` LIKE '%" . $this->getIsFilter() . "%'";
            return parent::getArticleByCategory($catId, $where);
        }

        return parent::getArticleByCategory($catId, $where);
    }
}
