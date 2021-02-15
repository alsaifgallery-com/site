<?php

namespace AlsaifGallery\Product\Plugin;

// use Magento\Framework\Api\AttributeValue;
class ProductRepository
{

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var Product[]
     */
    protected $instances = [];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $resourceModel;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $helperFactory;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    protected $brandAdapter;
    protected $scopeConfig;
    protected $productAttrRepo;
    protected $productExtentionFactory;
    protected $helper;

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product $resourceModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Catalog\Helper\ImageFactory $helperFactory,
        \Magento\Catalog\Api\Data\ProductExtensionInterfaceFactory $productExtentionFactory,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttrRepo,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \AlsaifGallery\Product\Adapters\BrandAdapterInterface $brandAdapter,
        \AlsaifGallery\Product\Api\Data\SpecificationsInterfaceFactory $specificationsFactory,   
        \AlsaifGallery\Product\Helper\Data $helper,
        \AlsaifGallery\AppConfigurations\Helper\Data $data    

    ) {
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->resourceModel = $resourceModel;
        $this->helperFactory = $helperFactory;
        $this->appEmulation = $appEmulation;
        $this->brandAdapter = $brandAdapter;
        $this->scopeConfig = $scopeConfig;
        $this->productAttrRepo = $productAttrRepo;
        $this->specificationsFactory = $specificationsFactory;
        $this->productExtentionFactory = $productExtentionFactory;
        $this->helper = $helper;
        $this->data=$data;
    }

    /**
     * {@inheritdoc}
     */
    private function get($sku, $editMode = false, $storeId = null, $forceReload = false)
    {
        $cacheKey = $this->getCacheKey([$editMode, $storeId]);
        if (!isset($this->instances[$sku][$cacheKey]) || $forceReload) {
            $product = $this->productFactory->create();

            $productId = $this->resourceModel->getIdBySku($sku);
            if (!$productId) {
                throw new NoSuchEntityException(__('Requested product doesn\'t exist'));
            }
            if ($editMode) {
                $product->setData('_edit_mode', true);
            }
            if ($storeId !== null) {
                $product->setData('store_id', $storeId);
            } else {
                // Start Custom code here

                $storeId = $this->storeManager->getStore()->getId();
            }
            $product->load($productId);

            $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);

            $imageUrl = $this->getImage($product, 'product_thumbnail_image')->getUrl();

            $customAttribute = $product->setCustomAttribute('thumbnail', $imageUrl);

            $this->appEmulation->stopEnvironmentEmulation();

            // End Custom code here

            $this->instances[$sku][$cacheKey] = $product;
            $this->instancesById[$product->getId()][$cacheKey] = $product;
        }
        return $this->instances[$sku][$cacheKey];
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    private function getImage($product, $imageId, $attributes = [])
    {
        $image = $this->helperFactory->create()->init($product, $imageId)
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepTransparency(true)
            ->keepFrame(false)
            ->resize(75, 75);

        return $image;
    }

    /**
     * @inheritdoc
     */
    public function afterGet(
        \Magento\Catalog\Api\ProductRepositoryInterface $object,
        $result, $sku, $editMode = false, $storeId = null, $forceReload = false ) {
        
        $res = $this->setNewProductData($result, $storeId);
        return $res;
    }

    private function setNewProductData($product, $storeId = null)
    {
      $base_image= '/placeholder/'.$this->data->getProductBaseImagePlaceholder();
      $thumbnail_image= '/placeholder/'.$this->data->getProductThumbnailImagePlaceholder();
        $ext = $product->getExtensionAttributes();
        if (is_null($ext)) {
            $ext = $this->productExtentionFactory->create();
        }
        if (!is_null($ext)) {
            $mediaURL = $this->storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\Url::URL_TYPE_MEDIA);
            $ext->setUrlBase(trim($mediaURL, "/") . "/catalog/product");
            $brand = $this->brandAdapter->getProductBrand($product->getId());
            if (!is_null($brand)) {
                $ext->setBrand($brand->getValue());
                $brandImage = $brand->getImage();

                if (!empty($brandImage)) {
                    $ext->setBrandImage(trim($mediaURL, "/") . $brandImage);
                } else {
                    // show default pic
                    // 'mageplaza/brand/default'
                    $defaultImage = $this->scopeConfig->getValue('shopbybrand/brandview/default_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                    if (empty($defaultImage)) {
                        $ext->setBrandImage("");
                    } else {
                        $ext->setBrandImage(trim($mediaURL, "/") . '/mageplaza/brand/' . $defaultImage);
                    }
                }
                                     $brandOptionId = $brand->getOptionId();
                $ext->setBrandOptionId($brandOptionId);
            }
            $imageUrl = $this->getImage($product, 'product_thumbnail_image')->getUrl();
            $ext->setDescription($product->getDescription());
            $ext->setShortDescription($product->getShortDescription());
            if(is_null($product->getThumbnail())){
            $ext->setThumbnail($thumbnail_image);
            }else{
            $ext->setThumbnail($product->getThumbnail());    
            }
            
            $specialPrice = $product->getSpecialPrice();
            $price = $product->getPrice();
            if ($price > 0) {
                if (isset($specialPrice) && $specialPrice >= 1) {
                    $ext->setSalePrice($product->getSpecialPrice());
                    $precent = 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - 100 - ($specialPrice / $price) * 100;
                    // $ext->setSalePercent($precent);
                    $ext->setSalePercent((int) $precent);
                }
            }

            // $ext->setOnsaleEnabled( $product->getOnsaleEnabled() );

            $onsaleEnabled = $product->getOnsaleEnabled();
            if (is_null($onsaleEnabled)) {
                $ext->setOnsaleEnabled("0");
            } else {
                $ext->setOnsaleEnabled($onsaleEnabled);
            }

// specifications
            $customAttributes = $product->getCustomAttributes();
            $specificationsArr = $ext->getSpecifications();
            $specificationsAvailableAttr = ['barcode','manufacturer','color',
                                            'size','count','suitable_for'];
            foreach ($customAttributes as $attribute) {
                // Magento\Framework\Api\AttributeValue
                // var_dump(get_class( $attribute));die;
                $code = $attribute->getAttributeCode();
                if(!in_array( $code ,  $specificationsAvailableAttr )){
                    continue;
                }
                $attributeObj = $this->productAttrRepo->get($code);
                if ($attributeObj->getId()) {
                    $inputType = $attributeObj->getFrontendInput();
                    // if(true){
                    if ( true || in_array($inputType, ["select", "multiselect", "boolean"])) {

                        $specifications = $this->specificationsFactory->create();
                        // $specifications->setKey($attributeObj->getAttributeCode());
                        $specifications->setKey($attributeObj->getDefaultFrontendLabel());
                        if ($inputType == "multiselect") {
                            if (is_array($product->getAttributeText($attributeObj->getAttributeCode()))) {
                                $specifications->setValue(
                                    implode(',', $product->getAttributeText($attributeObj->getAttributeCode()))
                                );
                            } else {
                                $specifications->setValue(
                                    $product->getAttributeText($attributeObj->getAttributeCode())
                                );
                            }
                        } else if(($inputType == "select") ||( $inputType == "boolean" ) ) {
                            $specifications->setValue(
                                $product->getAttributeText($attributeObj->getAttributeCode())
                            );
                        }else{
                            $specifications->setValue(
                                $product->getData($attributeObj->getAttributeCode())
                            );
                        }
                        $specificationsArr[] = $specifications;
                    }
                }
            }
            $ext->setSpecifications($specificationsArr);
// end specifications
            
            
            $isInCart = $this->helper->getIsProductInHeaderCustomerActiveCart($product->getId());
            $ext->setIsCartProduct( $isInCart );
            
            $isInWishList = $this->helper->getIsProductInHeaderCustomerWishlist($product->getId());
            $ext->setIsWishlistProduct( $isInWishList );
            
            $product->setExtensionAttributes($ext);
        }

        return $product;
    }

    private function setNewProductDataListing($product, $storeId = null)
    {
     $base_image= '/placeholder/'.$this->data->getProductBaseImagePlaceholder();
     $thumbnail_image= '/placeholder/'.$this->data->getProductThumbnailImagePlaceholder();
        $ext = $product->getExtensionAttributes();
        if (is_null($ext)) {
            $ext = $this->productExtentionFactory->create();
        }
        if (!is_null($ext)) {
            $mediaURL = $this->storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\Url::URL_TYPE_MEDIA);
            $ext->setUrlBase(trim($mediaURL, "/") . "/catalog/product");
            $brand = $this->brandAdapter->getProductBrand($product->getId());
            if (!is_null($brand)) {
                $ext->setBrand($brand->getValue());
                $brandImage = $brand->getImage();
                $brandOptionId = $brand->getOptionId();
                $ext->setBrandOptionId($brandOptionId);
                if (!empty($brandImage)) {
                    $ext->setBrandImage(trim($mediaURL, "/") . $brandImage);
                } else {
                    // show default pic
                    // 'mageplaza/brand/default'
                    $defaultImage = $this->scopeConfig->getValue('shopbybrand/brandview/default_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                    if (empty($defaultImage)) {
                        $ext->setBrandImage("");
                    } else {
                        $ext->setBrandImage(trim($mediaURL, "/") . '/mageplaza/brand/' . $defaultImage);
                    }
                }
            }

            // $ext->setDescription( $product->getDescription());
            // $ext->setShortDescription( $product->getShortDescription() );

            $specialPrice = $product->getSpecialPrice();
            $price = $product->getPrice();
            if ($price > 0) {
                if (isset($specialPrice) && $specialPrice >= 1) {
                    $ext->setSalePrice($product->getSpecialPrice());
                    $precent = 100 - ($specialPrice / $price) * 100;
                    // $ext->setSalePercent($precent);
                    $ext->setSalePercent((int) $precent);
                }
            }

            // $ext->setOnsaleEnabled( $product->getOnsaleEnabled() );
            $onsaleEnabled = $product->getOnsaleEnabled();
            if (is_null($onsaleEnabled)) {
                $ext->setOnsaleEnabled("0");
            } else {
                $ext->setOnsaleEnabled($onsaleEnabled);
            }
           if (is_null($product->getThumbnail())) {
                $ext->setThumbnail($thumbnail_image);
            } else {
                $ext->setThumbnail($product->getThumbnail());
            }
           
            $isInCart = $this->helper->getIsProductInHeaderCustomerActiveCart($product->getId());
            $ext->setIsCartProduct( $isInCart );

            $isInWishList = $this->helper->getIsProductInHeaderCustomerWishlist($product->getId());
            $ext->setIsWishlistProduct( $isInWishList );
            
            $product->setExtensionAttributes($ext);
        }

        return $product;
    }

    /**
     * @inheritdoc
     */
    public function afterGetById(
        \Magento\Catalog\Api\ProductRepositoryInterface $object,
        $result,
        $productId, $editMode = false, $storeId = null, $forceReload = false ) {
        
        if (!$editMode){
            $res = $this->setNewProductData($result, $storeId);
            return $res;
        }
        return $result ;
    }

    /**
     * @inheritdoc
     */
    public function afterGetList(
        \Magento\Catalog\Api\ProductRepositoryInterface $object,
        $result,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria) {
  // die("5555");
        $storeId = $this->storeManager->getStore()->getId();
        $itemsRet = [];
        $items = $result->getItems();
        foreach ($items as $product) {
            $itemsRet[] = $this->setNewProductDataListing($product, $storeId);
        }
        $result->setItems($itemsRet);

        return $result;
    }

}
