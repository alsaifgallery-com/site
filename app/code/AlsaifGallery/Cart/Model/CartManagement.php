<?php
 namespace AlsaifGallery\Cart\Model;

 use Magento\Quote\Model\Quote\Item\Repository as ItemRepository;
 use Psr\Log\LoggerInterface;
 use Magento\Catalog\Api\ProductRepositoryInterface;
 use AlsaifGallery\Cart\Model\GetCartForUser;
 use Magento\Quote\Api\CartRepositoryInterface;

 class CartManagement implements \AlsaifGallery\Cart\Api\CartManagementInterface
 {
     protected $cartManagement;

     public function __construct(
         \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
         \Magento\Framework\App\Request\Http $request,
         \Magento\Quote\Api\Data\CartItemExtensionFactory $cartItemExtensionFactory,
         \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
         LoggerInterface $logger,
         ProductRepositoryInterface $productRepository,
         \Magento\Catalog\Model\ProductFactory $productloader,
         \Magento\Store\Model\StoreManagerInterface $storeManager,
         \Magento\CatalogInventory\Api\StockStateInterface $stockState,
         \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
         \Magento\Quote\Api\CartManagementInterface  $cartManagement,
         GetCartForUser $getCartForCustomer,
         CartRepositoryInterface $cartRepository,

         \Magento\QuoteGraphQl\Model\Cart\GetCartForUser $getCartForUser,
         \Magento\Framework\Webapi\Rest\Request $restRequest
     ) {
         $this->quoteRepository = $quoteRepository;
         $this->request = $request;
         $this->stockRegistry = $stockRegistry;
         $this->cartItemExtensionFactory = $cartItemExtensionFactory;
         $this->logger = $logger;
         $this->productRepository = $productRepository;
         $this->productloader = $productloader;
         $this->storeManager = $storeManager;
         $this->stockState = $stockState;
         $this->scopeConfig = $scopeConfig;
         $this->cartManagement = $cartManagement;
         $this->getCartForUser = $getCartForUser;
         $this->getCartForCustomer = $getCartForCustomer;
         $this->cartRepository = $cartRepository;
         $this->restRequest = $restRequest;
     }

    /**
     * {@inheritdoc}
     */
     public function createEmptyCartForCustomer($customerId){

        $res = $this->cartManagement->createEmptyCartForCustomer($customerId);

        $validator = new \Zend\Validator\Digits();
        if(  $validator->isValid( $res )  ){
         $resArr["status"]=true;
         $resArr["quote_id"]=$res;
         $resArr["message"]=__("Obtaining Cart successed.")->render();
         return $res;
        }else{
         $resArr["status"]=false;
         $resArr["quote_id"]=0;
         $resArr["message"]=__("Obtaining Cart failed.")->render();
         return [$resArr];
        }

     }

     public function fetchCrossItem($productId) {
       $crossSellProduct = [];
       $currentStore = $this->storeManager->getStore();
       $url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
       $precent = "";$type = "";$price = "";$specialPrice = "";$itemImage = "";$thumbnail = "";$base_url = "";
       try {
           $product = $this->productRepository->get($productId);
           $crossSell = $product->getCrossSellProducts();
           if (count($crossSell)) {
               foreach ($crossSell as $productItem) {
                 $productDetails = $this->productloader->create()->load($productItem->getId());
                 $type = $productDetails->getTypeId();
                 if (in_array($type, ['simple', 'downloadable', 'virtual'])) {
                     $price = $productDetails->getPrice();
                     $specialPrice = $productDetails->getSpecialPrice();
                     if ($price > 0) {
                         if (isset($specialPrice) && $specialPrice >= 1) {
                             $precent = ($specialPrice / $price) * 100;
                             $precent = (int) $precent;
                         }
                     }
                 }
                 $base_url = $url . '/catalog/product';

                 try {
                     $stock = $this->stockState->verifyStock($productDetails->getId(), $currentStore->getId());
                 } catch (\Exception $e) {
                 }
                 if($image = $productDetails->getCustomAttribute('image')){
                   if(empty($image->getValue())){
                       if(empty($this->scopeConfig->getValue('catalog/placeholder/image_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $currentStore))){
                         $itemImage = $image->getValue();
                       }else{
                     $itemImage = '/placeholder/'. $this->scopeConfig->getValue('catalog/placeholder/image_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $currentStore);
                       }
                     }else{
                    $itemImage = $image->getValue();
                   }
                 }

                   $crossSellProduct[] = [
                     "sku" => $productDetails->getSku(),
                     "stock" => $stock,
                     "id" => $productDetails->getId(),
                     "name" => $productDetails->getName(),
                     "status" => $productDetails->getStatus(),
                     "type" => $type,
                     "Price" => $price,
                     "sale_price" => $specialPrice,
                     "sale_percent" => $precent,
                     "base_url" => $base_url,
                     "image" => $itemImage
                   ];
               }
           }
       } catch (\Exception $exception) {
           $this->logger->error($exception->getMessage());
           echo $exception->getMessage();
       }

       return $crossSellProduct;
     }

     /**
      * {@inheritdoc}
      */
     public function getCrossSellProducts()
     {
         $skuList = $this->restRequest->getBodyParams();
         $array = [];
         foreach ($skuList['items'] as $sku) {
           $array = array_merge($array, $this->fetchCrossItem($sku));

         }
         return $array;
     }


     /**
      * {@inheritdoc}
      */
      public function mergeCart($sourceCart,$destinationCart,$customerId) {

        $cartItems = [];
        if (empty($sourceCart)) {
            echo __('Required parameter "source_cart_id" is missing');
        }

        if (empty($destinationCart) || $destinationCart == null) {
            $destinationCart = $this->createEmptyCartForCustomer($customerId);
        }

        $storeId = (int)$this->storeManager->getStore()->getId();

        $guestMaskedCartId = $sourceCart;
        $customerMaskedCartId = $destinationCart;

        $currentUserId = $customerId;

        $guestCart = $this->getCartForUser->execute($guestMaskedCartId, null, $storeId);
        $customerCart = $this->getCartForCustomer->execute($customerMaskedCartId, $currentUserId, $storeId);
        $customerCart->merge($guestCart);
        $guestCart->setIsActive(false);
        $this->cartRepository->save($customerCart);
        $this->cartRepository->save($guestCart);
        $customerCart = (array) $customerCart;

        return [
            'model' => $customerCart
        ];

      }
 }
