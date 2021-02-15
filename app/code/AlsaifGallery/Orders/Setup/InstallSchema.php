<?php


namespace AlsaifGallery\Orders\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
           $installer = $setup;

    $installer->startSetup();

    $table = $installer->getTable('sales_order_status');

    $columns = [
        'is_balleh' => [
            'type' =>\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            'nullable' => false,
            'comment' => 'is_balleh',
        ],
        'icon' => [
            'type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'icon',
        ],

    ];

    $connection = $installer->getConnection();
    foreach ($columns as $name => $definition) {
        $connection->addColumn($table, $name, $definition);
    }

    $installer->endSetup();
    }
}
