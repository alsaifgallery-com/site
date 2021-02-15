<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AlsaifGallery\Cart\Plugin;
use Magento\Quote\Model\Quote\Item\Repository as ItemRepository;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Stock
{
    protected $quoteRepository;
    protected $cartItemExtensionFactory;
    protected $stockRegistry;


    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\Data\CartItemExtensionFactory $cartItemExtensionFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->stockRegistry = $stockRegistry;
        $this->cartItemExtensionFactory = $cartItemExtensionFactory;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->productloader = $productloader;
        $this->storeManager = $storeManager;
        $this->stockState = $stockState;
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundGetList(ItemRepository $subject, \Closure $proceed, $cartId)
    {
        $crossSell = ["crossSellItems" => []];
        $cartItemss = ["cartItems" => []];
        $cartItems = $proceed($cartId);
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        /** @var \Magento\Quote\Api\Data\CartItemExtensionInterface $extensionAttributes */
          $i = 0;
        foreach ($quote->getAllVisibleItems() as $item) {

            $storeId = $this->storeManager->getStore()->getId();
            $productId = $item->getProduct()->getId();
            $product = $this->productRepository->getById($productId, false , $storeId);
            $item->setName( $product->getName() );

            $extensionAttributes = $cartItems[$i]->getExtensionAttributes();
            if ($extensionAttributes === null) {
                $extensionAttributes = $this->cartItemExtensionFactory->create();
            }
            $sku = $item->getSku();
            $stock = $this->stockRegistry->getStockItemBySku($sku)->getIsInStock();
            if ($stock) {
                $message = "In Stock";
            } else {
                $message = "Out Of Stock";
            }
            $extensionAttributes->setStock($message);
            $cartItems[$i]->setExtensionAttributes($extensionAttributes);
            $i++;

        }


        return $cartItems;
    }

    /**
     * get cross sell products
     *
     * @return array
     */
    public function getCrosssellProductsList($id)
    {
        $productId = $id;
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
                              $precent = 100 - (int) $precent;
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
        }

        return $crossSellProduct;
    }
}
