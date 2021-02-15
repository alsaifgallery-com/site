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
 * @package     Mageplaza_SeoDashboard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoDashboard\Block\Adminhtml\Renderer\Duplicate;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ProductRepository;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\DataObject;
use Mageplaza\SeoDashboard\Helper\Data;

/**
 * Class View
 * @package Mageplaza\SeoDashboard\Block\Adminhtml\Renderer\Duplicate
 */
class View extends AbstractRenderer
{
    /**
     * Limit
     */
    const LIMIT = 3;

    /**
     * @type \Magento\Catalog\Model\ProductRepository|null
     */
    protected $_productRepository = null;

    /**
     * @type \Magento\Catalog\Model\CategoryRepository
     */
    protected $_categoryRepository;

    /**
     * @type \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @type \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        PageFactory $pageFactory,
        Context $context,
        array $data = []
    )
    {
        $this->_productRepository  = $productRepository;
        $this->_categoryRepository = $categoryRepository;
        $this->_pageFactory        = $pageFactory;
        $this->_urlBuilder         = $context->getUrlBuilder();

        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function render(DataObject $row)
    {
        $id     = $row['entity_id'];
        $result = '<ul>';

        switch ($row['entity']) {
            case Data::CATEGORY_ENTITY:
                $category = $this->_categoryRepository->get((int)$id, $row['store']);
                $result   .= '<li><a class="mp-db-issue" target="_blank" href="'
                    . $this->_urlBuilder->getUrl('catalog/category/edit', ['id' => (int)$id, 'store' => $row['store_id']])
                    . '">'
                    . htmlspecialchars($category->getName())
                    . '</a></li>';
                break;
            case Data::PAGE_ENTITY;
                $page   = $this->_pageFactory->create()->load((int)$id);
                $result .= '<li><a class="mp-db-issue" target="_blank" href="'
                    . $this->_urlBuilder->getUrl('cms/page/edit', ['page_id' => (int)$id])
                    . '">'
                    . htmlspecialchars($page->getTitle())
                    . '</a></li>';
                break;
            default:
                $product = $this->_productRepository->getById((int)$id, false, $row['store']);
                $result  .= '<li><a class="mp-db-issue" target="_blank" href="'
                    . $this->_urlBuilder->getUrl('catalog/product/edit', ['id' => (int)$id, 'store' => $row['store_id']])
                    . '">'
                    . htmlspecialchars($product->getName())
                    . '</a></li>';
        }

        $result .= '</ul>';

        return $result;
    }
}