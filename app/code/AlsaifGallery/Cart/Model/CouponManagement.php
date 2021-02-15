<?php

namespace AlsaifGallery\Cart\Model;

class CouponManagement implements \AlsaifGallery\Cart\Api\CouponManagementInterface {

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    protected $cartTotalsRepository;
    protected $couponManagement;

    public function __construct(
            \Magento\Quote\Api\CouponManagementInterface $couponManagement,
            \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository,
            \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->couponManagement = $couponManagement;
        $this->quoteRepository = $quoteRepository;
        $this->cartTotalsRepository = $cartTotalsRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $couponCode) {
        $res = $this->couponManagement->set($cartId, $couponCode);
        return $this->cartTotalsRepository->get($cartId); 
        
//        if ($res == true) {
//
//             $resArr['totals']= $this->getCurrentTotals($cartId);
//            $resArr["status"] = true;
//            $resArr["message"] = __("Adding coupon to cart successed.")->render();
//        } else {
//            $resArr["status"] = false;
//            $resArr["message"] = __("Adding coupon to cart failed.")->render();
//        }
//        return [$resArr];
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId) {
        $res = $this->couponManagement->remove($cartId);

        return $this->cartTotalsRepository->get($cartId); 
        
//        if ($res == true) {
//            $totals = $this->cartTotalsRepository->get($cartId);
//             $resArr['totals']= $this->getCurrentTotals($cartId);
//            $resArr["status"] = true;
//            $resArr["message"] = __("Removing coupon from Cart successed.")->render();
//        } else {
//            $resArr["status"] = false;
//            $resArr["message"] = __("Removing coupon from Cart failed.")->render();
//        }
//        return [$resArr];
    }
    
    public function getCurrentTotals($cartId){
          $totals = $this->cartTotalsRepository->get($cartId);
          $totals->setTotalSegments(array());
          $totals->setItems(array());
          if(!$totals->getCouponCode()){
              $totals->setCouponCode('');
          }
          return $totals->getData();

    }

}
