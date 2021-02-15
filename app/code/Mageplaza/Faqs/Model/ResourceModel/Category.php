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

namespace Mageplaza\Faqs\Model\ResourceModel;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Mageplaza\Faqs\Helper\Data;

/**
 * Class Category
 * @package Mageplaza\Faqs\Model\ResourceModel
 */
class Category extends AbstractDb
{
    /**
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var string
     */
    protected $_articleCategoryTable;

    /**
     * Category constructor.
     *
     * @param Context $context
     * @param ManagerInterface $eventManager
     * @param Data $helperData
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        ManagerInterface $eventManager,
        Data $helperData,
        $connectionName = null
    )
    {
        $this->_eventManager = $eventManager;
        $this->_helperData   = $helperData;

        parent::__construct($context, $connectionName);

        $this->_articleCategoryTable = $this->getTable('mageplaza_faqs_article_category');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_faqs_category', 'category_id');
    }

    /**
     * Retrieves Category Name from DB by passed ids.
     *
     * @param $ids
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryNameByIds($ids)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where("category_id IN (" . $ids . ")");

        return $adapter->fetchCol($select);
    }

    /**
     * Get all relationship category Ids
     *
     * @return array
     */
    public function getRelationCategoryIds()
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->_articleCategoryTable, 'category_id')
            ->group('category_id');

        return $adapter->fetchCol($select);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this|AbstractDb
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** save store Ids */
        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        /** generate URL Key */
        $object->setUrlKey(
            $this->_helperData->generateUrlKey($this, $object, $object->getUrlKey() ?: $object->getName())
        );

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return AbstractDb
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->saveArticleRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param \Mageplaza\Faqs\Model\Category $category
     * @return array
     */
    public function getArticleIds(\Mageplaza\Faqs\Model\Category $category)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()->from(
            $this->_articleCategoryTable,
            'article_id'
        )
            ->where(
                'category_id = ?',
                (int) $category->getId()
            );
        return $adapter->fetchCol($select);
    }

    /**
     * @param \Mageplaza\Faqs\Model\Category $category
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveArticleRelation(\Mageplaza\Faqs\Model\Category $category)
    {
        $category->setIsChangedArticleList(false);
        $id       = $category->getId();
        $articles = $category->getArticlesIds();

        if ($articles === null) {
            if ($category->getIsArticleGrid()) {
                $articles = [];
            }
            else {
                return $this;
            }
        }

        $articles    = array_keys($articles);
        $oldArticles = $category->getArticleIds();
        $insert      = array_diff($articles, $oldArticles);
        $delete      = array_diff($oldArticles, $articles);
        $adapter     = $this->getConnection();

        if (!empty($delete)) {
            $condition = ['article_id IN(?)' => $delete, 'category_id=?' => $id];
            $adapter->delete($this->_articleCategoryTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $articleId) {
                $data[] = [
                    'category_id' => (int) $id,
                    'article_id'  => (int) $articleId
                ];
            }
            $adapter->insertMultiple($this->_articleCategoryTable, $data);
        }
        if (!empty($insert) || !empty($delete)) {
            $articleIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'mageplaza_faqs_category_change_articles',
                ['category' => $category, 'article_ids' => $articleIds]
            );

            $category->setIsChangedArticleList(true);
            $articleIds = array_keys($insert + $delete);
            $category->setAffectedArticleIds($articleIds);
        }

        return $this;
    }
}
