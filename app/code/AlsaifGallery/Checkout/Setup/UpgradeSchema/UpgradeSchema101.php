<?php

namespace AlsaifGallery\Checkout\Setup\UpgradeSchema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema101 implements UpgradeSchemaInterface
{
    protected $qouteSetup;
    
    public function __construct(
        \Magento\Quote\Setup\QuoteSetup $qouteSetup
    ) {
        $this->qouteSetup = $qouteSetup;
    }
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {

        $installer->getConnection()->addColumn(
                $installer->getTable('quote_payment'), 
                'checkoutcom_data', [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => "checkoutcom_data",
                ]
        );

//        

    }
}