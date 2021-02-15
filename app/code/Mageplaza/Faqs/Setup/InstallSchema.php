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

namespace Mageplaza\Faqs\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Mageplaza\Faqs\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (!$installer->tableExists('mageplaza_faqs_article')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_faqs_article'))
                ->addColumn('article_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ], 'Article ID')
                ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Article Name')
                ->addColumn('author_name', Table::TYPE_TEXT, 255, [], 'Author Name')
                ->addColumn('author_email', Table::TYPE_TEXT, 255, [], 'Author Email')
                ->addColumn('status', Table::TYPE_TEXT, 255, [], 'Status')
                ->addColumn('visibility', Table::TYPE_TEXT, 255, [], 'Visibility')
                ->addColumn('article_content', Table::TYPE_TEXT, '64k', [], 'Article Content')
                ->addColumn('store_ids', Table::TYPE_TEXT, null, ['nullable' => false, 'unsigned' => true,], 'Store Id')
                ->addColumn('positives', Table::TYPE_INTEGER, 11, ['nullable' => false, 'unsigned' => true, 'default' => 0], 'Rate Positive')
                ->addColumn('negatives', Table::TYPE_INTEGER, 11, ['nullable' => false, 'unsigned' => true, 'default' => 0], 'Rate Negative')
                ->addColumn('views', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true, 'default' => 0], 'Article Views')
                ->addColumn('position', Table::TYPE_INTEGER, 11, [], 'Position')
                ->addColumn('url_key', Table::TYPE_TEXT, 255, [], 'Article URL Key')
                ->addColumn('in_rss', Table::TYPE_INTEGER, 1, [], 'Article In RSS')
                ->addColumn('email_notify', Table::TYPE_INTEGER, 1, [], 'Email Notification')
                ->addColumn('meta_title', Table::TYPE_TEXT, 255, [], 'Article Meta Title')
                ->addColumn('meta_description', Table::TYPE_TEXT, '64k', [], 'Article Meta Description')
                ->addColumn('meta_keywords', Table::TYPE_TEXT, '64k', [], 'Article Meta Keywords')
                ->addColumn('meta_robots', Table::TYPE_TEXT, 255, [], 'Article Meta Robots')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Article Updated At')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Article Created At')
                ->setComment('Article Table');

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('mageplaza_faqs_category')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_faqs_category'))
                ->addColumn('category_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ], 'Category ID')
                ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable => false'], 'Category Name')
                ->addColumn('description', Table::TYPE_TEXT, '64k', [], 'Category Description')
                ->addColumn('store_ids', Table::TYPE_TEXT, null, ['nullable' => false, 'unsigned' => true,], 'Store Id')
                ->addColumn('url_key', Table::TYPE_TEXT, 255, [], 'Category URL Key')
                ->addColumn('icon', Table::TYPE_TEXT, 255, [], 'Icon')
                ->addColumn('enabled', Table::TYPE_INTEGER, 1, [], 'Category Enabled')
                ->addColumn('meta_title', Table::TYPE_TEXT, 255, [], 'Category Meta Title')
                ->addColumn('meta_description', Table::TYPE_TEXT, '64k', [], 'Category Meta Description')
                ->addColumn('meta_keywords', Table::TYPE_TEXT, '64k', [], 'Category Meta Keywords')
                ->addColumn('meta_robots', Table::TYPE_TEXT, 255, [], 'Category Meta Robots')
                ->addColumn('position', Table::TYPE_INTEGER, null, [], 'Category Position')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Category Updated At')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Category Created At')
                ->setComment('Category Table');

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('mageplaza_faqs_article_category')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_faqs_article_category'))
                ->addColumn('category_id', Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                    'primary'  => true,
                    'nullable' => false
                ], 'Category ID')
                ->addColumn('article_id', Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                    'primary'  => true,
                    'nullable' => false
                ], 'Article ID')
                ->addIndex($installer->getIdxName('mageplaza_faqs_article_category', ['category_id']), ['category_id'])
                ->addIndex($installer->getIdxName('mageplaza_faqs_article_category', ['article_id']), ['article_id'])
                ->addForeignKey(
                    $installer->getFkName('mageplaza_faqs_article_category', 'category_id', 'mageplaza_faqs_category', 'category_id'),
                    'category_id',
                    $installer->getTable('mageplaza_faqs_category'),
                    'category_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('mageplaza_faqs_article_category', 'article_id', 'mageplaza_faqs_article', 'article_id'),
                    'article_id',
                    $installer->getTable('mageplaza_faqs_article'),
                    'article_id',
                    Table::ACTION_CASCADE
                )
                ->addIndex(
                    $installer->getIdxName('mageplaza_faqs_article_category', ['category_id', 'article_id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                    ['category_id', 'article_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->setComment('Category To Article Link Table');

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('mageplaza_faqs_article_product')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_faqs_article_product'))
                ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                    'primary'  => true,
                    'nullable' => false
                ], 'Entity ID')
                ->addColumn('article_id', Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                    'primary'  => true,
                    'nullable' => false
                ], 'Article ID')
                ->addIndex($installer->getIdxName('mageplaza_faqs_article_product', ['entity_id']), ['entity_id'])
                ->addIndex($installer->getIdxName('mageplaza_faqs_article_product', ['article_id']), ['article_id'])
                ->addForeignKey(
                    $installer->getFkName('mageplaza_faqs_article_product', 'entity_id', 'catalog_product_entity', 'entity_id'),
                    'entity_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('mageplaza_faqs_article_product', 'article_id', 'mageplaza_faqs_article', 'article_id'),
                    'article_id',
                    $installer->getTable('mageplaza_faqs_article'),
                    'article_id',
                    Table::ACTION_CASCADE
                )
                ->addIndex(
                    $installer->getIdxName('mageplaza_faqs_article_product', ['entity_id', 'article_id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                    ['entity_id', 'article_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->setComment('Article To Product Link Table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
