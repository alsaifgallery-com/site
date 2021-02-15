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

namespace Mageplaza\Faqs\Model\ResourceModel\Article\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Payment\Gateway\Http\Client\Zend;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 * @package Mageplaza\Faqs\Model\ResourceModel\Article\Grid
 */
class Collection extends SearchResult
{
    /**
     * Collection constructor.
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'mageplaza_faqs_article',
        $resourceModel = '\Mageplaza\Faqs\Model\ResourceModel\Article'
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->_addHelpfulRate();
        $this->_getCategoryIds();

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return mixed
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_filter') {
            $this->getSelect()->where("store_ids LIKE '%{$condition['eq']}%'");
            return $this;
        }
        else if ($field == 'category_filter') {
            $this->getSelect()->having("GROUP_CONCAT(`mpfac`.`category_id`) LIKE '%{$condition['eq']}%'");
            return $this;
        }
        else if ($field == 'helpful_rate') {
            $this->getSelect()->where("(`positives`/( `positives`+ `negatives`)*100) LIKE '{$condition['like']}'");
            return $this;
        }
        else if ($field == 'article_id') {
            $field = 'main_table.article_id';
        }
        else if ($field == 'author_name') {
            return parent::addFieldToFilter(['author_name', 'author_email'], [$condition, $condition]);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add helpful rate field to grid
     *
     * @return $this
     */
    protected function _addHelpfulRate()
    {
        $this->getSelect()->columns([
            'helpful_rate' => new \Zend_Db_Expr('`positives`+ `negatives`')
        ]);

        return $this;
    }

    /**
     * Add article category ids to grid
     *
     * @return $this
     */
    protected function _getCategoryIds()
    {
        $this->getSelect()->joinLeft(
            ['mpfac' => $this->getTable('mageplaza_faqs_article_category')],
            'main_table.article_id = mpfac.article_id',
            []
        )->columns([
            'categories' => new \Zend_Db_Expr('GROUP_CONCAT(`mpfac`.`category_id`)')
        ])->group('main_table.article_id');

        return $this;
    }
}
