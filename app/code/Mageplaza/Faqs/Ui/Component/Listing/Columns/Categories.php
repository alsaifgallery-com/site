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

namespace Mageplaza\Faqs\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\Faqs\Model\CategoryFactory;

/**
 * Class Categories
 * @package Mageplaza\Faqs\Ui\Component\Listing\Columns
 */
class Categories extends Column
{
    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Categories constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CategoryFactory $categoryFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CategoryFactory $categoryFactory,
        array $components = [],
        array $data = []
    )
    {
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        /** @var \Mageplaza\Faqs\Model\Category $categories */
        $category = $this->_categoryFactory->create();
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->_prepareItem($item, $category);
            }
        }

        return $dataSource;
    }

    /**
     * Get categories data
     *
     * @param array $item
     * @param \Mageplaza\Faqs\Model\Category $category
     * @return \Magento\Framework\Phrase|mixed
     */
    protected function _prepareItem(array $item, $category)
    {
        $content = '';
        if (isset($item['categories'])) {
            $categoryNames = $category->getResource()->getCategoryNameByIds($item['categories']);
            foreach ($categoryNames as $name) {
                $content .= $name . '<br/>';
            }
        }

        return $content;
    }
}
