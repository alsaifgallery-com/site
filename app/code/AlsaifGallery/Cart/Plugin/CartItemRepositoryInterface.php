<?php

namespace AlsaifGallery\Cart\Plugin;


class CartItemRepositoryInterface
{
    protected $productRepository;
    protected $storeManager;
    
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    public function afterGetList(
        \Magento\Quote\Api\CartItemRepositoryInterface $subject,    
        $resault,
        $cartId
    ){
        
        $storeId = $this->storeManager->getStore()->getId();
       
        foreach( $resault as $item ){
            $productId = $item->getProduct()->getId();
            $product = $this->productRepository->getById($productId, false , $storeId);
            $item->setName( $product->getName() );
        }
        return $resault;
    }

}
