<?php
namespace AlsaifGallery\Category\Setup; 
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory.
     *
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;
    protected $categorySetupFactory;
 
    /**
     * Init.
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory, \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory)
    {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }
 
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
        $setup = $this->categorySetupFactory->create(['setup' => $setup]);         
        $setup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 'thumbnail_image', [
                'type'     => 'varchar',
                'label'    => 'Thumbnail Image',
                'input'    => 'image',
                'backend'  => \Magento\Catalog\Model\Category\Attribute\Backend\Image::class,
                'visible'  => false,
                'default'  => '0',
                'required' => false,
                'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
            ]
        );

        $setup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 'slider', [
                'type'     => 'int',
                'label'    => 'Slider',
                'input'    => 'select',
                'source'   => \AlsaifGallery\Category\Model\Config\Source\Sliders::class,
                'visible'  => true,
                'default'  => '0',
                'required' => false,
                'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
            ]
        );

    }
}