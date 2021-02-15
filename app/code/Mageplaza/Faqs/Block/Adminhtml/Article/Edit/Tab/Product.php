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

namespace Mageplaza\Faqs\Block\Adminhtml\Article\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Registry;

/**
 * Class Product
 * @package Mageplaza\Faqs\Block\Adminhtml\Article\Edit\Tab
 */
class Product extends Extended implements TabInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public $productCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * Product constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
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
        $this->productCollectionFactory = $productCollectionFactory;
        $this->coreRegistry             = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('product_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);

        if ($this->getArticle()->getId()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collection */
        $collection = $this->productCollectionFactory->create();
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
        $this->addColumn('in_products', [
                'header_css_class' => 'a-center',
                'type'             => 'checkbox',
                'name'             => 'in_product',
                'values'           => $this->_getSelectedProducts(),
                'align'            => 'center',
                'index'            => 'entity_id'
            ]
        );
        $this->addColumn('entity_id', [
                'header'           => __('ID'),
                'sortable'         => true,
                'index'            => 'entity_id',
                'type'             => 'number',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn('name', [
                'header'           => __('Name'),
                'index'            => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name',
            ]
        );
        $this->addColumn('sku', [
                'header'           => __('Sku'),
                'index'            => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku'
            ]
        );
        $this->addColumn('price', [
                'header'           => __('Price'),
                'type'             => 'currency',
                'index'            => 'price',
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price',
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
     * Get selected product
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('article_products', null);
        if (!is_array($products)) {
            $products = $this->getArticle()->getProductIds();

            return array_combine($products, $products);
        }

        return $products;
    }

    /**
     * Get selected products. This is callback function when clicking filter product
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSelectedProducts()
    {
        $selected = $this->getArticle()->getProductIds();
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
        return $this->getUrl('*/*/productsGrid', ['article_id' => $this->getArticle()->getId()]);
    }

    /**
     * @return \Mageplaza\Faqs\Model\Article
     */
    public function getArticle()
    {
        return $this->coreRegistry->registry('mageplaza_faqs_article');
    }

    /**
     * Apply the product grid filter
     *
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            }
            else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
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
        return __('Product');
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
        return $this->getUrl('mpfaqs/article/products', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
