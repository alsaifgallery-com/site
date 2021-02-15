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
use Mageplaza\Faqs\Model\Config\Source\Status;

/**
 * Class Article
 * @package Mageplaza\Faqs\Model\ResourceModel
 */
class Article extends AbstractDb
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
     * @var string
     */
    protected $_articleProductTable;

    /**
     * Article constructor.
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
        $this->_articleProductTable  = $this->getTable('mageplaza_faqs_article_product');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_faqs_article', 'article_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this|AbstractDb
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** save store Ids */
        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        /** generate URL Key */
        $object->setUrlKey(
            $this->_helperData->generateUrlKey($this, $object, $object->getUrlKey() ?: $object->getName())
        );

        /** Set status depend on the answer */
        if ($object->getArticleContent() == '') {
            $object->setStatus(Status::PENDING);
        }
        else {
            $object->setStatus(Status::ANSWERED);
        }

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
        $this->saveCategoryRelation($object);
        $this->saveProductRelation($object);

        /** save single product relation */
        if ($object->getData('product_id') && $object->getData('product_id') != '') {
            $productData = [
                'article_id' => (int) $object->getId(),
                'entity_id'  => $object->getData('product_id')
            ];

            $this->getConnection()
                ->insert($this->_articleProductTable, $productData);
        }

        return parent::_afterSave($object);
    }

    /**
     * @param \Mageplaza\Faqs\Model\Article $article
     * @return array
     */
    public function getCategoryIds(\Mageplaza\Faqs\Model\Article $article)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from(
                $this->_articleCategoryTable,
                'category_id'
            )
            ->where(
                'article_id = ?',
                (int) $article->getId()
            );

        return $adapter->fetchCol($select);
    }

    /**
     * @param \Mageplaza\Faqs\Model\Article $article
     * @return array
     */
    public function getProductIds(\Mageplaza\Faqs\Model\Article $article)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()->
        from(
            $this->_articleProductTable,
            'entity_id'
        )
            ->where(
                'article_id = ?',
                (int) $article->getId()
            );

        return $adapter->fetchCol($select);
    }

    /**
     * @param \Mageplaza\Faqs\Model\Article $article
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveCategoryRelation(\Mageplaza\Faqs\Model\Article $article)
    {
        $article->setIsChangedCategoryList(false);
        $id         = $article->getId();
        $categories = $article->getCategoriesIds();

        if ($categories === null) {
            return $this;
        }

        $oldCategoryIds = $article->getCategoryIds();
        $insert         = array_diff($categories, $oldCategoryIds);
        $delete         = array_diff($oldCategoryIds, $categories);
        $adapter        = $this->getConnection();

        if (!empty($delete)) {
            $condition = ['category_id IN(?)' => $delete, 'article_id=?' => $id];
            $adapter->delete($this->_articleCategoryTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $categoryId) {
                $data[] = [
                    'article_id'  => (int) $id,
                    'category_id' => (int) $categoryId,
                ];
            }
            $adapter->insertMultiple($this->_articleCategoryTable, $data);
        }
        if (!empty($insert) || !empty($delete)) {
            $categoryIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'mageplaza_faqs_article_change_categories',
                ['article' => $article, 'category_ids' => $categoryIds]
            );
        }
        if (!empty($insert) || !empty($delete)) {
            $article->setIsChangedCategoryList(true);
            $categoryIds = array_keys($insert + $delete);
            $article->setAffectedCategoryIds($categoryIds);
        }

        return $this;
    }

    /**
     * @param \Mageplaza\Faqs\Model\Article $article
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveProductRelation(\Mageplaza\Faqs\Model\Article $article)
    {
        $article->setIsChangedProductList(false);
        $id       = $article->getId();
        $products = $article->getProductsIds();

        if ($products === null) {
            if ($article->getIsProductGrid()) {
                $products = [];
            }
            else {
                return $this;
            }
        }

        $products    = array_keys($products);
        $oldProducts = $article->getProductIds();
        $insert      = array_diff($products, $oldProducts);
        $delete      = array_diff($oldProducts, $products);
        $adapter     = $this->getConnection();

        if (!empty($delete)) {

            $condition = ['entity_id IN(?)' => $delete, 'article_id=?' => $id];
            $adapter->delete($this->_articleProductTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $entityId) {
                $data[] = [
                    'article_id' => (int) $id,
                    'entity_id'  => (int) $entityId
                ];
            }
            $adapter->insertMultiple($this->_articleProductTable, $data);
        }
        if (!empty($insert) || !empty($delete)) {
            $entityIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'mageplaza_faqs_article_change_products',
                ['article' => $article, 'entity_ids' => $entityIds]
            );
        }
        if (!empty($insert) || !empty($delete)) {
            $article->setIsChangedProductList(true);
            $entityIds = array_keys($insert + $delete);
            $article->setAffectedProductIds($entityIds);
        }

        return $this;
    }
}
