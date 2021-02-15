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

namespace Mageplaza\Faqs\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Mageplaza\Faqs\Model\ResourceModel\Article\CollectionFactory;

/**
 * Class Article
 * @package Mageplaza\Faqs\Block\Adminhtml\Category\Edit\Tab
 */
class Article extends Extended implements TabInterface
{
    /**
     * @var \Mageplaza\Faqs\Model\ResourceModel\Article\CollectionFactory
     */
    public $articleCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * Article constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Data $backendHelper
     * @param CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Data $backendHelper,
        CollectionFactory $productCollectionFactory,
        array $data = []
    )
    {
        $this->articleCollectionFactory = $productCollectionFactory;
        $this->coreRegistry             = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('article_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);

        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(['in_articles' => 1]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var \Mageplaza\Faqs\Model\ResourceModel\Article\Collection $collection */
        $collection = $this->articleCollectionFactory->create();
        $collection->addFieldToSelect('*');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_articles', [
                'header_css_class' => 'a-center',
                'type'             => 'checkbox',
                'name'             => 'in_article',
                'values'           => $this->_getSelectedArticles(),
                'align'            => 'center',
                'index'            => 'article_id'
            ]
        );
        $this->addColumn('article_id', [
                'header'           => __('ID'),
                'sortable'         => true,
                'index'            => 'article_id',
                'type'             => 'number',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn('name', [
                'header'           => __('Question Name'),
                'index'            => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name',
            ]
        );
        $this->addColumn('created_at', [
                'header'           => __('Created At'),
                'index'            => 'created_at',
                'type'             => 'date',
                'header_css_class' => 'col-created',
                'column_css_class' => 'col-created',
            ]
        );
        $this->addColumn('updated_at', [
                'header'           => __('Updated At'),
                'index'            => 'updated_at',
                'type'             => 'date',
                'header_css_class' => 'col-updated',
                'column_css_class' => 'col-updated',
            ]
        );
        $this->addColumn('position', [
                'header'           => __('Position'),
                'name'             => 'position',
                'header_css_class' => 'hidden',
                'column_css_class' => 'hidden',
                'validate_class'   => 'validate-number',
                'index'            => 'position',
                'editable'         => true,
            ]
        );

        return $this;
    }

    /**
     * Get selected article
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getSelectedArticles()
    {
        $articles = $this->getRequest()->getPost('category_articles', null);
        if (!is_array($articles)) {
            $articles = $this->getCategory()->getArticleIds();

            return array_combine($articles, $articles);
        }

        return $articles;
    }

    /**
     * Get selected articles. This is callback function when clicking filter article
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSelectedArticles()
    {
        $selected = $this->getCategory()->getArticleIds();
        if (!is_array($selected)) {
            $selected = [];
        }

        return array_combine($selected, $selected);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/articlesGrid', ['category_id' => $this->getCategory()->getId()]);
    }

    /**
     * @return \Mageplaza\Faqs\Model\Category
     */
    public function getCategory()
    {
        return $this->coreRegistry->registry('mageplaza_faqs_category');
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_articles') {
            $articleIds = $this->_getSelectedArticles();
            if (empty($articleIds)) {
                $articleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('article_id', ['in' => $articleIds]);
            }
            else {
                if ($articleIds) {
                    $this->getCollection()->addFieldToFilter('article_id', ['nin' => $articleIds]);
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Question');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('mpfaqs/category/articles', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
