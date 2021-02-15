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
 * Class Category
 *
 * @method Category setArticlesIds(array $data)
 * @method Category setIsChangedArticleList(\bool $flag)
 * @method Category setIsArticleGrid(\bool $flag)
 * @method Category setAffectedArticleIds(array $ids)
 * @method bool getIsArticleGrid()
 * @method array getArticlesIds()
 * @package Mageplaza\Faqs\Model
 */
class Category extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_faqs_category';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_faqs_category';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_faqs_category';

    /**
     * @var string
     */
    protected $_idFieldName = 'category_id';

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Category constructor.
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
        $this->_init('Mageplaza\Faqs\Model\ResourceModel\Category');
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
    public function getArticleIds()
    {
        if (!$this->hasData('article_ids')) {
            $ids = $this->_getResource()->getArticleIds($this);
            $this->setData('article_ids', $ids);
        }

        return (array) $this->_getData('article_ids');
    }

    /**
     * @return bool|string
     */
    public function getUrl()
    {
        return $this->_helperData->getFaqsUrl($this, Data::TYPE_CATEGORY);
    }
}
