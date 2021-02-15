<?php

namespace AlsaifGallery\Product\Helper;
use Magento\Framework\App\RequestInterface;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $customerRepo;
    protected $quoteRepository;
    protected $cartItemRepo;
    
    protected $remoteAddress;
    protected $quoteResource;
    protected $quoteFactory;
    protected $cartManagment;
    
    protected $storeManager;
    protected $wishListRepo;
    protected $wishlistHelper;

    /**
     * @param Context $context
     */
    public function __construct(
            \Magento\Framework\App\Helper\Context $context,
            \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo,
            \Magento\Quote\Api\CartItemRepositoryInterface $cartItemRepo,
            
            \Magento\Store\Model\StoreManagerInterface  $storeManager,
            
            \Magento\Quote\Api\CartManagementInterface $cartManagment,
            \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
            \Magento\Quote\Model\QuoteFactory $quoteFactory,
            \Magento\Quote\Model\ResourceModel\Quote  $quoteResource,
            
            \WebbyTroops\WishlistAPI\Api\WishlistRepositoryInterface $wishListRepo,
            \WebbyTroops\WishlistAPI\Helper\Data $wishlistHelper

            
     ){
        $this->customerRepo = $customerRepo;
        $this->quoteRepository = $quoteRepository;
        $this->cartItemRepo = $cartItemRepo;
        $this->cartManagment = $cartManagment;
        $this->quoteFactory = $quoteFactory;
        $this->quoteResource = $quoteResource;
        $this->storeManager = $storeManager;
        $this->wishListRepo = $wishListRepo;
        $this->wishlistHelper = $wishlistHelper;
        
        parent::__construct($context);
    }

     public function getHeaderCustomerId(){
        $customer =  $this->_getRequest()->getHeader("customer");
        if(  $customer == false ){
            $customer =  $this->_getRequest()->getHeader("Customer");
        }
        return $customer;
     }
     
     public function getHeaderCustomer(){
        $customerId =  $this->getHeaderCustomerId();
        if(  $customerId != false ){
            $customer = $this->customerRepo->getById( $customerId );
            if( !is_null($customer) && $customer->getId() ){
                return $customer;
            }
        }
        return false;
     }
     
     public function getHeaderCustomerActiveCart(){
        $customerId =  $this->getHeaderCustomerId();
        return $this->cartRepo->getActiveForCustomer($customerId);
     }
     
     public function getIsProductInHeaderCustomerActiveCart($productId){
        $customerId =  $this->getHeaderCustomerId();
        // return false;
        
        if ( $customerId != false ){
            // $customer = $this-> getHeaderCustomer();
//          $cartItems = $this->cartItemRepo->getList(  $cartId );
            
            $quote  = $this->quoteFactory->create();
            $this->quoteResource->loadByCustomerId($quote,$customerId); // where `$customerId` is your `customer id`
            // var_dump(count($quote->getAllVisibleItems()) );die; 
            $items= $quote->getAllVisibleItems();
            foreach ($items as $cartItem) {
                if( $cartItem->getProductId() == $productId ){
                    return true;
                }
            }
        }
        return false;
     }
     
    public function getHeaderCustomerActiveCartCount(){
        $customerId =  $this->getHeaderCustomerId();
        // return false;
        
        if ( $customerId != false ){
          
            $quote  = $this->quoteFactory->create();
            $this->quoteResource->loadByCustomerId($quote,$customerId); // where `$customerId` is your `customer id`
            // var_dump(count($quote->getAllVisibleItems()) );die; 
            // $items= $quote->getAllVisibleItems();
            // return count( $items );
            return (float)$quote->getItemsQty();
        }
        return 0 ;
     }
     
     public function getIsProductInHeaderCustomerWishlist($productId){
        $customerId =  $this->getHeaderCustomerId();
        // return false;
        if ( $customerId != false ){

            $wishlist = $this->wishlistHelper
                             ->checkWishlistProduct( $productId , $customerId);
            $isWishListProduct = ($wishlist->hasData()) ? true : false;
            return $isWishListProduct;

        }
        return false;
     }
     
     
     
           /**
     * Creates a cart for the currently logged-in customer.
     *
     * @param int $customerId
     * @param int $storeId
     * @return \Magento\Quote\Model\Quote Cart object.
     * @throws CouldNotSaveException The cart could not be created.
     */
    protected function createCustomerCart($customerId, $storeId)
    {
        try {
            
            // $quote = $this->quoteRepository->getActiveForCustomer($customerId);
            $sharedStoreIds = [];
            $quote = $this->quoteRepository->getForCustomer($customerId, $sharedStoreIds);
            if (!$quote->getIsActive()) {
                throw \Magento\Framework\Exception\NoSuchEntityException::singleField('customerId', $customerId);
            }
        
       } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // var_dump($e->getMessage());die;
            $customer = $this->customerRepo->getById($customerId);
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteFactory->create();
            $quote->setStoreId($storeId);
            $quote->setCustomer($customer);
            $quote->setCustomerIsGuest(0);
            // $quote->setRemoteIp($this->remoteAddress->getRemoteAddress());
        }
        return $quote;
    }
    
    public function createEmptyCartForCustomer($customerId)
    {
        $storeId = $this->storeManager->getStore()->getStoreId();
        
        $quote = $this->createCustomerCart($customerId, $storeId);

        try {
            $this->quoteResource->save($quote);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__("The quote can't be created."));
        }
        return (int)$quote->getId();
    }
    
   
    
}