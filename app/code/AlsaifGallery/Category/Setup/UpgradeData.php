<?php
namespace AlsaifGallery\Category\Setup;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{

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


	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
			  /** @var EavSetup $eavSetup */
              $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
              $setup = $this->categorySetupFactory->create(['setup' => $setup]);

              if (version_compare($context->getVersion(), '1.0.7', '<')) {

              $setup->addAttribute(
                  \Magento\Catalog\Model\Category::ENTITY, 'icon', [
                      'type'     => 'varchar',
                      'label'    => 'Icon',
                      'input'    => 'image',
                      'backend'  => \Magento\Catalog\Model\Category\Attribute\Backend\Image::class,
                      'visible'  => true,
                      'default'  => '0',
                      'required' => false,
                      'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                      'group' => 'General Information',
                  ]
              );

              $setup->addAttribute(
                  \Magento\Catalog\Model\Category::ENTITY, 'is_featured', [
                      'type'     => 'int',
                      'label'    => 'Is Featured',
                      'input'    => 'boolean',
                      'sort_order' => 3,
                      'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                      'visible'  => true,
                      'default'  => '0',
                      'required' => false,
                      'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                      'group' => 'General Information',
                  ]
              );

		}
            if (version_compare($context->getVersion(), '1.0.7', '<')) {
                // AlsaifGallery\Category\Model\Config\Backend\Featured

                $setup->addAttribute(
                  \Magento\Catalog\Model\Category::ENTITY, 'is_featured', [
                      'type'     => 'int',
                      'label'    => 'Is Featured',
                      'input'    => 'boolean',
                      'sort_order' => 3,
                      'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                      'backend'  => 'AlsaifGallery\Category\Model\Config\Backend\Featured',
                      'visible'  => true,
                      'default'  => '0',
                      'required' => false,
                      'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                      'group' => 'General Information',
                  ]
              );

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
            if (version_compare($context->getVersion(), '1.0.7', '<')) {

                $setup->addAttribute(
                  \Magento\Catalog\Model\Category::ENTITY, 'brands', [
                      'type'     => 'text',
                      'label'    => 'Brands',
                      'input'    => 'multiselect',
                      'sort_order' => 4,
                      'source'   => \AlsaifGallery\Category\Model\Config\Source\Brands::class,
                      'backend'   => \AlsaifGallery\Category\Model\Config\Source\BrandsBackend::class,
                      'input_renderer'   => \AlsaifGallery\Category\Model\Config\Source\BrandsInputRenderer::class,
                      'visible'  => true,
                      'default'  => '0',
                      'required' => false,
                      'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                      'group' => 'General Information',
                  ]
              );

              $attributeSet = $setup->getDefaultAttributeSetId( \Magento\Catalog\Model\Category::ENTITY );
                $attributeGroup = $setup->getDefaultAttributeGroupId(\Magento\Catalog\Model\Category::ENTITY);
                $setup->addAttributeToGroup(
                    \Magento\Catalog\Model\Category::ENTITY,
                    $attributeSet,
                    $attributeGroup,
                    $setup->getAttributeId( \Magento\Catalog\Model\Category::ENTITY , 'brands')
                );


            }
	}
}
