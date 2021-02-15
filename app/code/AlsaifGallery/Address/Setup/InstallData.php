<?php


namespace AlsaifGallery\Address\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;

class InstallData implements InstallDataInterface
{

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

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        // $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
        //     'address_label', [
        //         'label' => 'Address Label',
        //         'input' => 'text',
        //         'type' => 'varchar',
        //         'source' => '',
        //         'required' => false,
        //         'position' => 333,
        //         'visible' => true,
        //         'system' => false,
        //         'is_used_in_grid' => false,
        //         'is_visible_in_grid' => false,
        //         'is_filterable_in_grid' => false,
        //         'is_searchable_in_grid' => false,
        //         'backend' => ''
        //     ]);
        //
        // $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'address_label')
        // ->addData(['used_in_forms' => [
        //         'adminhtml_customer_address',
        //         'customer_address_edit',
        //         'customer_register_address'
        //     ]
        // ]);
        // $attribute->save();
        //-------------------

    //     $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
    //     'phone_verified', [
    //         'label' => 'Is Phone verified',
    //         'input' => 'text',
    //         'type' => 'varchar',
    //         'source' => '',
    //         'required' => false,
    //         'position' => 333,
    //         'visible' => true,
    //         'system' => false,
    //         'is_used_in_grid' => false,
    //         'is_visible_in_grid' => false,
    //         'is_filterable_in_grid' => false,
    //         'is_searchable_in_grid' => false,
    //         'backend' => ''
    //     ]);
    //
    // $attributePhoneVerified = $customerSetup->getEavConfig()->getAttribute('customer_address', 'phone_verified')
    // ->addData(['used_in_forms' => [
    //         'adminhtml_customer_address',
    //         'customer_address_edit',
    //         'customer_register_address'
    //     ]
    // ]);
    // $attributePhoneVerified->save();
    //
    //     //-------------
    //     $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
    //     'address_latitude', [
    //         'label' => 'Address Latitude',
    //         'input' => 'text',
    //         'type' => 'varchar',
    //         'source' => '',
    //         'required' => false,
    //         'position' => 333,
    //         'visible' => true,
    //         'system' => false,
    //         'is_used_in_grid' => false,
    //         'is_visible_in_grid' => false,
    //         'is_filterable_in_grid' => false,
    //         'is_searchable_in_grid' => false,
    //         'backend' => ''
    //     ]);
    //
    // $attributeLatitude = $customerSetup->getEavConfig()->getAttribute('customer_address', 'address_latitude')
    // ->addData(['used_in_forms' => [
    //         'adminhtml_customer_address',
    //         'customer_address_edit',
    //         'customer_register_address'
    //     ]
    // ]);
    // $attributeLatitude->save();

    // ----------------------

//     $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
//     'address_longitude', [
//         'label' => 'Address Longitude',
//         'input' => 'text',
//         'type' => 'varchar',
//         'source' => '',
//         'required' => false,
//         'position' => 333,
//         'visible' => true,
//         'system' => false,
//         'is_used_in_grid' => false,
//         'is_visible_in_grid' => false,
//         'is_filterable_in_grid' => false,
//         'is_searchable_in_grid' => false,
//         'backend' => ''
//     ]);
//
// $attributeLongitude = $customerSetup->getEavConfig()->getAttribute('customer_address', 'address_longitude')
// ->addData(['used_in_forms' => [
//         'adminhtml_customer_address',
//         'customer_address_edit',
//         'customer_register_address'
//     ]
// ]);
// $attributeLongitude->save();



    }
}
