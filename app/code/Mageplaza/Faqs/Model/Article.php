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

namespace Mageplaza\Faqs\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Mageplaza\Faqs\Helper\Data;

/**
 * Class Article.
 *
 * @method Article setIsChangedCategoryList(\bool $flag)
 * @method Article setIsChangedProductList(\bool $flag)
 * @method Article setIsProductGrid(\bool $flag)
 * @method Article setAffectedCategoryIds(array $ids)
 * @method Article setAffectedProductIds(array $ids)
 * @method Article setProductsIds(array $data)
 * @method Article setCategoriesIds(array $categoryIds)
 * @method array getCategoriesIds()
 * @method array getProductsIds()
 * @method bool getIsProductGrid()
 * @package Mageplaza\Faqs\Model
 */
class Article extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_faqs_article';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_faqs_article';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_faqs_article';

    /**
     * @var string
     */
    protected $_idFieldName = 'article_id';

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Article constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Data $helperData
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helperData,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_helperData = $helperData;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\Faqs\Model\ResourceModel\Article');
    }

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryIds()
    {
        if (!$this->hasData('category_ids')) {
            $ids = $this->_getResource()->getCategoryIds($this);
            $this->setData('category_ids', $ids);
        }

        return (array) $this->_getData('category_ids');
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductIds()
    {
        if (!$this->hasData('product_ids')) {
            $ids = $this->_getResource()->getProductIds($this);
            $this->setData('product_ids', $ids);
        }

        return (array) $this->_getData('product_ids');
    }

    /**
     * @return bool|string
     */
    public function getUrl()
    {
        return $this->_helperData->getFaqsUrl($this, Data::TYPE_ARTICLE);
    }

    /**
     * Get article short answer
     *
     * @return string
     */
    public function getShortAnswer()
    {
        $limitChar   = ((int) $this->_helperData->getConfigGeneral('question_detail_page/max_char')) ?: 255;
        $shortAnswer = ($this->getArticleContent() && $limitChar > 0) ? $this->getArticleContent() : '';
        if (strlen($shortAnswer) > $limitChar) {
            $shortAnswer = mb_substr($shortAnswer, 0, $limitChar, mb_detect_encoding($shortAnswer)) . '...';
        }

        return $shortAnswer;
    }
}
