<?php
 namespace AlsaifGallery\Cart\Model;
 
 class CartItem implements \AlsaifGallery\Cart\Api\CartItemInterface
 {
     protected $cartItemReop;
     public function __construct(
        \Magento\Quote\Api\CartItemRepositoryInterface  $cartItemReop
     ) {
         $this->cartItemReop = $cartItemReop;
     }

    /**
     * {@inheritdoc}
     */
     public function deleteById($cartId, $itemId){
         
        $res = $this->cartItemReop->deleteById($cartId, $itemId);
        
        if( $res == true ){
         $resArr["status"]=true;
         $resArr["message"]=__("Removing Cart item successed.")->render();   
        }else{
         $resArr["status"]=false;
         $resArr["message"]=__("Removing Cart item failed.")->render();
        }
        return [$resArr];
     }
 }
