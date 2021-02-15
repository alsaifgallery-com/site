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

namespace Mageplaza\Faqs\Model\ResourceModel\Category\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 * @package Mageplaza\Faqs\Model\ResourceModel\Category\Grid
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
        $mainTable = 'mageplaza_faqs_category',
        $resourceModel = '\Mageplaza\Faqs\Model\ResourceModel\Category'
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

        $this->_getQuestionNum();

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return mixed
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'question_num') {
            $condition = str_replace('%', '', $condition['like']);
            if (is_numeric($condition)) {
                $this->getSelect()->having("COUNT(`mpfac`.`category_id`) = {$condition}");
            }
            else {
                $this->getSelect()->having("COUNT(`mpfac`.`category_id`) LIKE '{$condition}'");
            }
            return $this;
        }
        else if ($field == 'category_id') {
            $field = 'main_table.category_id';
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add question num to category grid
     *
     * @return $this
     */
    protected function _getQuestionNum()
    {
        $this->getSelect()->joinLeft(
            ['mpfac' => $this->getTable('mageplaza_faqs_article_category')],
            'main_table.category_id = mpfac.category_id',
            []
        )->columns([
            'question_num' => new \Zend_Db_Expr('COUNT(`mpfac`.`category_id`)')
        ])->group('main_table.category_id');

        return $this;
    }
}
