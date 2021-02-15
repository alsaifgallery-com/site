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

namespace Mageplaza\Faqs\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Faqs\Helper\Data;

/**
 * Class Collection
 * @package Mageplaza\Faqs\Model\ResourceModel\Category
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'category_id';

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Data $helperData
     * @param StoreManagerInterface $storeManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Data $helperData,
        StoreManagerInterface $storeManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->_storeManager = $storeManager;
        $this->_helperData   = $helperData;
    }

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\Faqs\Model\Category', 'Mageplaza\Faqs\Model\ResourceModel\Category');
    }

    /**
     * Add if filter
     *
     * @param $categoryIds
     * @return $this
     */
    public function addIdFilter($categoryIds)
    {
        $condition = '';

        if (is_array($categoryIds)) {
            if (!empty($categoryIds)) {
                $condition = ['in' => $categoryIds];
            }
        }
        elseif (is_numeric($categoryIds)) {
            $condition = $categoryIds;
        }
        elseif (is_string($categoryIds)) {
            $ids = explode(',', $categoryIds);
            if (empty($ids)) {
                $condition = $categoryIds;
            }
            else {
                $condition = ['in' => $ids];
            }
        }

        if ($condition != '') {
            $this->addFieldToFilter('category_id', $condition);
        }

        return $this;
    }

    /**
     * Get visible question num each category
     *
     * @param null $where
     * @return $this
     */
    public function getQuestionNum($where = null)
    {
        $this->getSelect()->joinLeft(
            ['mpfac' => $this->getTable('mageplaza_faqs_article_category')],
            'main_table.category_id = mpfac.category_id',
            []
        )->joinLeft(
            ['mpfa' => $this->getTable('mageplaza_faqs_article')],
            'mpfac.article_id = mpfa.article_id',
            []
        )->columns([
            'question_num' => new \Zend_Db_Expr('COUNT(`mpfac`.`category_id`)')
        ])->group('main_table.category_id')->where('`mpfa`.`visibility` = 1 ' . $where);
        $this->addStoreFilter($this, $storeId = null);

        return $this;
    }

    /**
     * Filter by store
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @param null $storeId
     * @return mixed
     */
    public function addStoreFilter($collection, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->_storeManager->getStore()->getId();
        }

        $collection->addFieldToFilter('mpfa.store_ids', [
            ['finset' => Store::DEFAULT_STORE_ID],
            ['finset' => $storeId]
        ]);

        return $collection;
    }
}
