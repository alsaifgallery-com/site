<?php
/**
 * Copyright © 2018-2019 Nmcit , Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AlsaifGallery\Product\Observer;

use Magento\Catalog\Api\CategoryRepositoryInterface as CategoryRepository;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\Data\CartItemExtensionFactory;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

class ProductInterface implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     *@var \Magento\Catalog\Helper\ImageFactory
     */
    protected $productImageHelper;

    /**
     *@var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *@var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     * @var CartItemExtensionFactory
     */
    protected $extensionFactory;

    protected $categoryRepository;

    protected $data;
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ProductRepository $productRepository
     * @param \Magento\Catalog\Helper\ImageFactory
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Store\Model\App\Emulation
     * @param CartItemExtensionFactory $extensionFactory
     */
    public function __construct(
        \AlsaifGallery\AppConfigurations\Helper\Data $data,    
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        ProductImageHelper $productImageHelper,
        StoreManager $storeManager,
        AppEmulation $appEmulation,
        CartItemExtensionFactory $extensionFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->productRepository = $productRepository;
        $this->productImageHelper = $productImageHelper;
        $this->storeManager = $storeManager;
        $this->appEmulation = $appEmulation;
        $this->extensionFactory = $extensionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->data=$data;
    }

    public function execute(\Magento\Framework\Event\Observer $observer, string $imageType = null)
    {
        
        $thumbnail_image_placeholder= '/placeholder/'.$this->data->getProductThumbnailImagePlaceholder();
        $quote = $observer->getQuote();

        /**
         * Code to add the items attribute to extension_attributes
         */
        foreach ($quote->getAllItems() as $quoteItem) {
            $product = $this->productRepository->create()->getById($quoteItem->getProductId());

            $productExt = $product->getExtensionAttributes();
            if (!is_null($productExt)) {
                $itemExtAttr = $quoteItem->getExtensionAttributes();
                if ($itemExtAttr === null) {
                    $itemExtAttr = $this->extensionFactory->create();
                }
                $brand = $productExt->getBrand();
                if (!is_null($brand)) {
                    $itemExtAttr->setBrand($brand);
                }
                $brandImage = $productExt->getBrandImage();
                if (!is_null($brandImage)) {
                    $itemExtAttr->setBrandImage($brandImage);
                }
                $urlBase = $productExt->getUrlBase();
                if (!is_null($urlBase)) {
                    $itemExtAttr->setUrlBase($urlBase);
                }
                $thumbnail = $productExt->getThumbnail();
                if (!is_null($thumbnail)) {
                    $itemExtAttr->setThumbnail($thumbnail);
                }else{
                   if(!is_null($thumbnail_image_placeholder)){
                     $itemExtAttr->setThumbnail($thumbnail_image_placeholder);  
                   } 
                }

                $quoteItem->setExtensionAttributes($itemExtAttr);
            }
        }
        return;
    }

    /**
     * Helper function that provides full cache image url
     * @param \Magento\Catalog\Model\Product
     * @return string
     */
    protected function getImageUrl($product, string $imageType = null)
    {
        $storeId = $this->storeManager->getStore()->getId();

        $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $imageUrl = $this->productImageHelper->create()->init($product, $imageType)->getUrl();

        $this->appEmulation->stopEnvironmentEmulation();

        return $imageUrl;
    }

}
