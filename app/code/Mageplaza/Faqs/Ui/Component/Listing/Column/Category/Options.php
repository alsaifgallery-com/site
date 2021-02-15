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

namespace Mageplaza\Faqs\Ui\Component\Listing\Column\Category;

use Magento\Framework\Data\OptionSourceInterface;
use Mageplaza\Faqs\Model\CategoryFactory;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Options constructor.
     *
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        CategoryFactory $categoryFactory
    )
    {
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_generateCategoryOptions();
    }

    /**
     * Get category options
     *
     * @return array
     */
    protected function _generateCategoryOptions()
    {
        $options = [];
        /** @var \Mageplaza\Faqs\Model\Category $category */
        $category    = $this->_categoryFactory->create();
        $categoryIds = $category->getResource()->getRelationCategoryIds();
        if ($categoryIds) {
            $categoryName = $category->getResource()->getCategoryNameByIds(implode(',', $categoryIds));
            $categories   = array_combine($categoryIds, $categoryName);

            foreach ($categories as $id => $name) {
                $options[] = [
                    'label' => $name,
                    'value' => $id,
                ];
            }

        }
        return $options;
    }
}
