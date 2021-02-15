<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InstallData
 *
 * @author nada
 */
namespace AlsaifGallery\OrderOrigin\Setup;
 
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;

class InstallData implements InstallDataInterface {
    //put your code here
     protected $salesSetupFactory;
     
     public function __construct(
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
    }
     public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) 
    {
        $installer = $setup;

        $installer->startSetup();

        $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $installer]);

        $salesSetup->addAttribute(Order::ENTITY, 'order_origin', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> 255,
            'visible' => false,
            'nullable' => true
        ]);

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'order_origin',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' =>'Order Origin'
            ]
        );

        $installer->endSetup();
    }
}

