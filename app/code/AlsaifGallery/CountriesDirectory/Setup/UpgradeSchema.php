<?php

namespace AlsaifGallery\CountriesDirectory\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * {@inheritdoc}
     */
    public function upgrade(
            SchemaSetupInterface $setup,
            ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), "1.0.0", "<")) {
            $tableName = $setup->getTable('directory_country');
            if ($setup->getConnection()->isTableExists($tableName) == true) {

// Declare data

                $columns = [
                    'country_tele_code' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Country Tele Code',
                    ],
                ];

                $connection = $setup->getConnection();

                foreach ($columns as $name => $definition) {

                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }


        $setup->endSetup();
    }

}
