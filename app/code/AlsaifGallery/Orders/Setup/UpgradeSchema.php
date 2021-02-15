<?php


namespace AlsaifGallery\Orders\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), "1.1.0", "<")) {
    
            $this->addSortOrderCoulmn($setup);
        }
    }
    protected function addSortOrderCoulmn($setup) {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getTable('sales_order_status');

        $columns = [
            'sort_order' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'sort_order',
            ]
        ];

        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($table, $name, $definition);
        }

        $installer->endSetup();
    }

}