<?php
namespace AlsaifGallery\DailyDeals\Plugin\Magento\Catalog\Model;

class ProductRepository {

    public $productExtensionFactory;
    public $productFactory;
    
    public $dealManagement;
    
   
    
    public function __construct(
            \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory,
            \Magento\Catalog\Model\ProductFactory $productFactory,
            \AlsaifGallery\DailyDeals\Model\DealManagement $dealManagement
    ) {
        $this->productFactory = $productFactory;
        $this->productExtensionFactory = $productExtensionFactory;
        $this->dealManagement=$dealManagement;
    }

    public function afterGet(
            \Magento\Catalog\Api\ProductRepositoryInterface $object,
            $result, $sku, $editMode = false,
            $storeId = null, $forceReload = false
    ) {

        $productExtension = $result->getExtensionAttributes();
        if (null === $productExtension) {
            $productExtension = $this->productExtensionFactory->create();
        }
        $deal= $this->dealManagement->getProductDeal($result->getId());
        $productExtension->setDeal($deal);
        $result->setExtensionAttributes($productExtension);
        return $result;
    }
     public function afterGetList(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Framework\Api\SearchResults $searchResult
    ) {
      
        foreach ($searchResult->getItems() as $product) {
        $productExtension = $product->getExtensionAttributes();
        if (null === $productExtension) {
            $productExtension = $this->productExtensionFactory->create();
        }
        $deal= $this->dealManagement->getProductDeal($product->getId());
        $productExtension->setDeal($deal);
        $product->setExtensionAttributes($productExtension);  
        }
        return $searchResult;
    }

}