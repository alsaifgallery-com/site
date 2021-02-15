<?php

namespace AlsaifGallery\Product\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\Data\ProductLinkExtensionFactory;
use Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks as LinksInitializer;
use Magento\Catalog\Model\Product\LinkTypeProvider;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductLinkRepository 
{

    protected $productRepository;
    protected $productLinkFactory;
    protected $productLinkExtensionFactory;
    protected $helper;
    protected $data;
    /**
     * Constructor
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\Data\ProductLinkInterfaceFactory|null $productLinkFactory
     * @param \Magento\Catalog\Api\Data\ProductLinkExtensionFactory|null $productLinkExtensionFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \AlsaifGallery\Product\Helper\Data $helper,
        \Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $productLinkFactory = null,
        \Magento\Catalog\Api\Data\ProductLinkExtensionFactory $productLinkExtensionFactory = null,
        \AlsaifGallery\AppConfigurations\Helper\Data $data    
    ) {
        $this->productRepository = $productRepository;
        $this->helper = $helper;
        $this->data=$data;
        $this->productLinkFactory = $productLinkFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Catalog\Api\Data\ProductLinkInterfaceFactory::class);
        $this->productLinkExtensionFactory = $productLinkExtensionFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Catalog\Api\Data\ProductLinkExtensionFactory::class);
    }


    public function afterGetList(
            \Magento\Catalog\Api\ProductLinkRepositoryInterface $object,
            $resault,
            \Magento\Catalog\Api\Data\ProductInterface $product
    ){
        $base_image = '/placeholder/' . $this->data->getProductBaseImagePlaceholder();
        $thumbnail_image = '/placeholder/' . $this->data->getProductThumbnailImagePlaceholder();
        // Magento\Catalog\Model\ProductLink\Link 
        // var_dump(get_class($resault[0]));die;
        foreach ($resault as $link) {
            $linkExt = $link->getExtensionAttributes();
            if (is_null($linkExt)) {
                $linkExt = $this->productLinkExtensionFactory->create();
            }
            // $product = $this->productRepository->get($link->getSku());
            $product = $this->productRepository->get( $link->getLinkedProductSku() );
            $productExt = $product->getExtensionAttributes();
            if (!is_null($productExt)) {
                $linkExt->setSku( $link->getLinkedProductSku());
                $linkExt->setBrand($productExt->getBrand());
                $linkExt->setBrandImage($productExt->getBrandImage());
                $linkExt->setName($product->getName());
                $linkExt->setPrice($product->getPrice());
                $linkExt->setTypeId($product->getTypeId());
                $linkExt->setSalePrice($productExt->getSalePrice());
                $linkExt->setSalePercent($productExt->getSalePercent());
                
                // $linkExt->setOnsaleEnabled($productExt->getOnsaleEnabled());
                $onsaleEnabled =  $productExt->getOnsaleEnabled();
                if(is_null($onsaleEnabled)){
                   $linkExt->setOnsaleEnabled("0");
                }else{
                   $linkExt->setOnsaleEnabled($onsaleEnabled );
                }
                 if(is_null($product->getThumbnail()) || $product->getThumbnail() == 'no_selection'){
                  $linkExt->setThumbnail( $thumbnail_image);   
                 }else{
                  $linkExt->setThumbnail( $product->getThumbnail());   
                 }
                 if(is_null($product->getImage()) || $product->getImage() == 'no_selection'){
                   $linkExt->setImage($base_image);  
//                   $linkExt->setImage('true');  
                 }else{
                   $linkExt->setImage( $product->getImage());  
                 }
//                 $linkExt->setThumbnail( $product->getThumbnail());
//                 $linkExt->setImage( $product->getImage());
//                $imgAttr = $product->getCustomAttribute('image');
//                if(!is_null($imgAttr) ){
//                    $linkExt->setImage($productExt->getUrlBase() . $imgAttr->getValue());
//                }
                $isWishlistProduct = $productExt->getIsWishlistProduct();
//                var_dump ( !isset($isWishlistProduct) , empty($isWishlistProduct) );die();
                // var_dump($isWishlistProduct);
                if(empty($isWishlistProduct)){
                   $isWishlistProduct = false; 
                }
                
                if( $isWishlistProduct == false ){
                    $linkExt->setIsWishlistProduct(FALSE);
                }else if ($isWishlistProduct == true ){
                    $linkExt->setIsWishlistProduct(TRUE);
                }
                
//                $linkExt->setSpecifications( $productExt->getSpecifications() );
//                $linkExt->setdescription( $productExt->getdescription() );
//                $linkExt->setReviews( $productExt->getReviews() );
                $isInCart = $this->helper->getIsProductInHeaderCustomerActiveCart($product->getId());
                $linkExt->setIsCartProduct( $isInCart );
                
                $isInWishList = $this->helper->getIsProductInHeaderCustomerWishlist($product->getId());
                $linkExt->setIsWishlistProduct( $isInWishList );
            
            
                $link->setExtensionAttributes($linkExt);
            }
        }
        
        return $resault;
    }

}
