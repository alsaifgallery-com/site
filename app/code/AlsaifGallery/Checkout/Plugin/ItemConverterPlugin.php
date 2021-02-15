<?php
namespace AlsaifGallery\Checkout\Plugin;
// namespace Magento\Quote\Model\Cart\Totals;

/**
 * Cart item totals converter Plugin.
 *
 * @codeCoverageIgnore
 */
class ItemConverterPlugin
{
 
protected $extensionAttributesFactory;
protected $productRepo;
    public function __construct(
            \Magento\Quote\Api\Data\TotalsItemExtensionInterfaceFactory $extensionAttributesFactory,
            \Magento\Catalog\Api\ProductRepositoryInterface $productRepo
    ) {
        $this->extensionAttributesFactory = $extensionAttributesFactory ;   
        $this->productRepo = $productRepo;
    }
    
    /**
     * Converts a specified rate model to a shipping method data object.
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Magento\Quote\Api\Data\TotalsItemInterface
     * @throws \Exception
     */
    public function afterModelToDataObject(
            \Magento\Quote\Model\Cart\Totals\ItemConverter $subject,
            $resault,
            \Magento\Quote\Model\Quote\Item $item            
    ){
          $ext = $resault->getExtensionAttributes();  
          if(is_null($ext)){
            $ext = $this->extensionAttributesFactory->create();  
          }
          $product = $this->productRepo->getById( $item->getProduct()->getId() );
          if( $product->getId() ){
             $productExt=  $product->getExtensionAttributes();
            if(!is_null($productExt)){
                $ext->setUrlBase( $productExt->getUrlBase() );
                $ext->setThumbnail( $productExt->getThumbnail());
            }
          }
          $resault->setExtensionAttributes($ext);
          
        return $resault;
    }

}
