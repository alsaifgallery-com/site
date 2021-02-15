<?php

namespace AlsaifGallery\Address\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Customer\Setup\CustomerSetupFactory;

class UpgradeData implements UpgradeDataInterface {

    private $customerSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
            CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            // $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            //         'phone_code', [
            //     'label' => 'Phone code',
            //     'input' => 'text',
            //     'type' => 'varchar',
            //     'source' => '',
            //     'required' => false,
            //     'position' => 333,
            //     'visible' => true,
            //     'system' => false,
            //     'is_used_in_grid' => false,
            //     'is_visible_in_grid' => false,
            //     'is_filterable_in_grid' => false,
            //     'is_searchable_in_grid' => false,
            //     'backend' => ''
            // ]);
            //
            // $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'phone_code')
            //         ->addData(['used_in_forms' => [
            //         'adminhtml_customer_address',
            //         'customer_address_edit',
            //         'customer_register_address'
            //     ]
            // ]);
            // $attribute->save();
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            // $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            //         'extra_info', [
            //     'label' => 'Extra Info',
            //     'input' => 'text',
            //     'type' => 'varchar',
            //     'source' => '',
            //     'required' => false,
            //     'position' => 333,
            //     'visible' => true,
            //     'system' => false,
            //     'is_used_in_grid' => false,
            //     'is_visible_in_grid' => false,
            //     'is_filterable_in_grid' => false,
            //     'is_searchable_in_grid' => false,
            //     'backend' => ''
            // ]);
            //
            // $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'extra_info')
            //         ->addData(['used_in_forms' => [
            //         'adminhtml_customer_address',
            //         'customer_address_edit',
            //         'customer_register_address'
            //     ]
            // ]);
            // $attribute->save();
        }
    }

}
