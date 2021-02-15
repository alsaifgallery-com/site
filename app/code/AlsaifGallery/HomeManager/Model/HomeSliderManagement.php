<?php

namespace AlsaifGallery\HomeManager\Model;

class HomeSliderManagement implements \AlsaifGallery\HomeManager\Api\HomeSliderManagementInterface
{

    public $scopeConfig;

    public $storeManager;

    public $sliderAdapter;

    public $sliderFactory;
    protected $bannerFactory;

    public $categoryRepo;

    public $cateManager;

    public $proRepo;

    public $searchBuilder;

    public $attribute;

    public $productRepo;

    public $stockItemRepository;

    protected $brandAdapter;
    protected $categotyCollectionFactory;
    protected $stockState;
    protected $data;
    protected $productHelper;
    protected $limit = 8;
    protected $shopByBrandCount = 0;
    protected $shopByCatCount = 0 ;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \AlsaifGallery\Category\Adapters\SliderInterface $sliderAdapter,
        \Mageplaza\BannerSlider\Model\ResourceModel\Slider\CollectionFactory $sliderFactory,
        \Mageplaza\BannerSlider\Model\ResourceModel\Banner\CollectionFactory $bannerFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepo,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categotyCollectionFactory,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $cateManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $proRepo,
        \Magento\Catalog\Model\ProductRepository $productRepo,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attribute,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \AlsaifGallery\HomeManager\Adapters\BrandAdapterInterface $brandAdapter,
         \AlsaifGallery\Product\Helper\Data $productHelper,
        \Mageplaza\DailyDeal\Helper\Data $data
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->sliderAdapter = $sliderAdapter;
        $this->sliderFactory = $sliderFactory;
        $this->bannerFactory = $bannerFactory;
        $this->categoryRepo = $categoryRepo;
        $this->cateManager = $cateManager;
        $this->proRepo = $proRepo;
        $this->productRepo = $productRepo;
        $this->searchBuilder = $searchBuilder;
        $this->attribute = $attribute;
        $this->stockItemRepository = $stockItemRepository;
        $this->brandAdapter = $brandAdapter;
        $this->categotyCollectionFactory = $categotyCollectionFactory;
        $this->stockState = $stockState;
        $this->productHelper = $productHelper;
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getHomeSlider()
    {
        //cart count
        $returnCartCountData = [
             "cart_count"=>  $this->productHelper->getHeaderCustomerActiveCartCount(),
             "customer_id"=> $this->productHelper->getHeaderCustomerId(),
        ];
        $cartCountFlag = 0 ;

        $returnData = [];
        //$returnData [] = $returnCartCountData;
        $data = $this->parseConfiguiration();

        foreach ($data as $key => $item) {
            // var_dump( $key , $item);die;
            if (isset($item['slider'])) {
//         $returnData[]=$this->getSliderData( $item['slider'] );
                $slider = $this->getSliderData($item['slider']);
                if( $cartCountFlag <= 0 ){
                   $slider["cart_count"] =$this->productHelper->getHeaderCustomerActiveCartCount();
                   $cartCountFlag++;
                }else{
                    unset(  $slider["cart_count"] );
                }
                if (count($slider['data']) >= 1) {
                    $returnData[] = $slider;
                }
            }
            if (isset($item['banner'])) {
//         $returnData[]=$this->getBannerData($item['banner']);
                $banner = $this->getBannerData($item['banner']);
                if (count($banner['data']) >= 1) {
                    $returnData[] = $banner;
                }
            }
            if (isset($item['category'])) {
//          $returnData[]= $this->getCatData( $item['category'] );
                $category = $this->getCatData($item['category']);
                if (count($category['data']) >= 1) {
                    $returnData[] = $category;
                }
            }
            if (isset($item['shop_by_brand'])) {
                $shopByBrand = $this->getShopByBrandData($item['shop_by_brand']);
                if (count($shopByBrand['data']) >= 1) {
                    $returnData[] = $shopByBrand;
                }
            }
            if (isset($item['shop_by_cat'])) {
//          $returnData[]= $this->getShopByCatData(  $item['shop_by_cat']);
                $shopByCat = $this->getShopByCatData($item['shop_by_cat']);
                if (count($shopByCat['data']) >= 1) {
                    $returnData[] = $shopByCat;
                }
            }
        }

        return $returnData;

    }
    public function getHomeSliderOld()
    {

        $returnData = array(
            'slider' => array(),
            'category' => array(),
        );
        $data = $this->parseConfiguiration();
        if ($data['slider'] != null) {
            $returnData['slider'] = $this->getSliderData($data['slider']);
        }
        if ($data['category'] != null) {
            $returnData['category'] = $this->getCatData($data['category']);
        }
        return $returnData;

    }

    public function getShopByBrandData($data)
    {
        $this->shopByBrandCount =  $this->shopByBrandCount + 1;

        if ($data = "shop_by_brand") {
            // default
        } else {
            // got id
        }
        $store = $this->storeManager->getStore();
        $defaultImage = $this->scopeConfig->getValue('shopbybrand/brandview/default_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        // var_dump($defaultImage);die;

//        $limit = $this->limit;
        $limit=$this->scopeConfig->getValue('appconfigurations_setting/configs/homebrandslimit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        $currentStore = $this->storeManager->getStore();
        $url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $returnData = [
            'component' => 'shop-by-brand',
            'id' => $this->shopByBrandCount,
            'name' => __("Shop by brand"),
            'data' => [],
        ];
        $brands = $this->brandAdapter->getBrands();
        // var_dump($returnData[0]->getData());die;
        foreach ($brands as $brand) {
            if ($limit <= 0 || $limit == null) {
                break;
            }
            $brandArr = [];
            $brandArr['brand_id'] = $brand->getBrandId();
            $brandArr['brand_title'] = $brand->getPageTitle();
            $brandArr['option_id'] = $brand->getOptionId();
            $brandArr['attribute_id'] = $brand->getAttributeId();
            $brandArr['url_key'] = $brand->getUrlKey();
            $brandArr['image'] = $brand->getImage();
            $brandArr['sortPosition'] = $brand->getPosition();
            if (!empty($brandArr['image'])) {
                $brandArr['image'] = $url . $brandArr['image'];
            } else {
                // show default pic
                // 'mageplaza/brand/default'
                $defaultImage = $this->scopeConfig->getValue('shopbybrand/brandview/default_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
                if (empty($defaultImage)) {
                    $brandArr['image'] = "";
                } else {
                    $brandArr['image'] = trim($url, "/") . '/mageplaza/brand/' . $defaultImage;
                }
            }
            $brandArr['target'] = [
                "key" => 'option_id',
                "value" => $brand->getOptionId(),
            ];

            $returnData['data'][] = $brandArr;
            $limit--;
        }
        return $returnData;

    }

    public function getCatData($id)
    {

        $returnData = [
            'component' => 'product-slider',
            "id" => null,
            'name' => null,
            'data' => [],
        ];

        try {
            $category = $this->categoryRepo->get($id);
        } catch (\Exception $e) {
            return $returnData;
        }

        if (!$category->getId()) {
            return $returnData;
        }
        $currentStore = $this->storeManager->getStore();
        $limit=$this->scopeConfig->getValue('appconfigurations_setting/configs/homeproductslimit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $currentStore);

        $url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $this->searchBuilder->setCurrentPage(1);
        $this->searchBuilder->setPageSize($limit);
        $this->searchBuilder->addFilter('category_id', $id);
        $searchCriteria = $this->searchBuilder->create();

        $returnData = [
            'component' => 'product-slider',
            "id" => $id,
            'name' => $category->getName(),
            'data' => [],
        ];
        $data = [];
        $resault = $this->productRepo->getList($searchCriteria)->getItems();
        foreach ($resault as $r) {

            $data['sku'] = $r->getSku();
//            $data['stock'] = $r->getExtensionAttributes()->getStockItem()->getIsInStock();
            // $data['stock'] = $this->stockItemRepository->get($r->getId())->getIsInStock();
            try {
                // needs to be reviewed
                // $data['stock'] = $this->stockItemRepository->get($r->getId())->getIsInStock();
                $data['stock'] = $this->stockState->verifyStock($r->getId(), $currentStore->getId());
            } catch (\Exception $e) {
                // nothing
            }
            $data['id'] = $r->getId();
            $data['name'] = $r->getName();
            $data['status'] = $r->getStatus();

            // $onsaleEnabled = $r->getOnsaleEnabled();
            // if (is_null($onsaleEnabled)) {
            //     $data['onsale_enabled'] = "0";
            // } else {
            //     $data['onsale_enabled'] = $onsaleEnabled;
            // }

            $data['type'] = $r->getTypeId();
            if (in_array($data['type'], ['simple', 'downloadable', 'virtual'])) {
                $price = $r->getPrice();
                $data['price'] = $price;
                $specialPrice = $r->getSpecialPrice();
                if ($price > 0) {
                    if (isset($specialPrice) && $specialPrice >= 1) {
                        $data['sale_price'] = $specialPrice;
                        $precent = ($specialPrice / $price) * 100;
                        // $data['sale_percent'] = $precent;
                        $data['sale_percent'] = 100 - (int) $precent;
                    }
                }
            }
            $data['base_url'] = $url . '/catalog/product';
            // $data['is_wishlist_product'] = $r->getExtensionAttributes()->getIsWishlistProduct();
            // $data['is_cart_product'] = $r->getExtensionAttributes()->getIsCartProduct();
            if(empty($r->getExtensionAttributes()->getThumbnail())){
                if(empty($this->scopeConfig->getValue('catalog/placeholder/thumbnail_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $currentStore))){
                  $data['thumbnail'] = $r->getExtensionAttributes()->getThumbnail();
                }else{
              $data['thumbnail']='/placeholder/'. $this->scopeConfig->getValue('catalog/placeholder/thumbnail_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $currentStore);
                }
                }else{
              $data['thumbnail'] = $r->getExtensionAttributes()->getThumbnail();
            }
            if($image=$r->getCustomAttribute('image')){
            if(empty($image->getValue())){
                if(empty($this->scopeConfig->getValue('catalog/placeholder/image_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $currentStore))){
                  $data['image'] = $image->getValue();
                }else{
              $data['image']='/placeholder/'. $this->scopeConfig->getValue('catalog/placeholder/image_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $currentStore);
                }
                }else{
             $data['image'] = $image->getValue();
            }
            }
//            $data['image'] = $url . '/catalog/product' . $r->getCustomAttribute('image')->getValue();

            $brand = $this->brandAdapter->getProductBrand($r->getId());
            if (!is_null($brand)) {
                $mediaURL = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\Url::URL_TYPE_MEDIA);
                $data['brand'] = $brand->getValue();

                $brandImage = $brand->getImage();
                if (!empty($brandImage)) {
                    $data['brand_image'] = trim($mediaURL, "/") . $brandImage;
                } else {
                    // 'mageplaza/brand/default'
                    $defaultImage = $this->scopeConfig->getValue(
                        'shopbybrand/brandview/default_image',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $this->storeManager->getStore()->getId());

                    if (empty($defaultImage)) {
                        $data['brand_image'] = "";
                    } else {
                        $data['brand_image'] = trim($mediaURL, "/") . '/mageplaza/brand/' . $defaultImage;
                    }
                }
            }

            array_push($returnData["data"], $data);
        }
        return $returnData;
//        return $this->cateManager->getAssignedProducts($id);
        //////        $category=$this->categoryRepo->get($id);
        //////        return $category;
    }
//

    /**
     * @return  slider data
     */
    public function getSliderData($id)
    {
        $returnData = [
            'cart_count'=>0,
            'component' => 'slider',
            "id" => $id,
            'name' => null,
            'data' => [],
        ];

        $collection = $this->sliderFactory->create()->addActiveFilter();
        $slider = $collection->addIdFilter($id)->getFirstItem();
        // var_dump($slider->getData());die;
        if ($slider->getId()) {
            $banners = $this->sliderAdapter->getSliderBannerCollection($id);
            if ($banners != false) {
                $returnData = [
                    'cart_count'=>0,
                    'component' => 'slider',
                    "id" => $slider->getId(),
                    'name' => $slider->getName(),
                    'data' => [],
                ];
                if (count($banners) <= 1) {
                    $returnData['component'] = 'banner';
                }
                foreach ($banners as $banner) {

                    $bannerDataItem = [
                        'banner_id' => $banner->getBannerId(),
                        'image' => $banner->getImageUrl(),
                        'position' => $banner->getPosition(),
                        'name' => $banner->getName(),
                        'target' => $banner->getTarget(),
                        'target_id' => $banner->getTargetId()
                    ];
                    $targetDataStr = $banner->getUrlBanner();

                    $targetDataArr = explode('=', $targetDataStr);
                    if (count($targetDataArr) >= 2) {
                        $bannerDataItem['target'] = [
                            'key' => $targetDataArr[0],
                            'value' => $targetDataArr[1],
                        ];
                    }
                    $returnData['data'][] = $bannerDataItem;
                }
                return $returnData;
            }
        } else {
            return $returnData;
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Sorry this slider does not exist')
            );
        }
    }
    /**
     * @return  banner data
     */
    public function getBannerData($data)
    {
        $returnData = [
            'component' => 'banner',
            "id" => null,
            'name' => __("Banner."),
            'data' => [],
        ];

        $id = 0;
        if ($data == "banner") {
            return $returnData;
        } else {
            $id = $data;
            $returnData["id"] = $id;
        }
        $collection = $this->bannerFactory
            ->create()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('banner_id', $id);

        $banner = $collection->getFirstItem();
        if ($banner->getId()) {

            $bannerDataItem = [
                'banner_id' => $banner->getBannerId(),
                'image' => $banner->getImageUrl(),
                'name' => $banner->getName(),
                'target' => $banner->getTarget(),
                'target_id' => $banner->getTargetId()
            ];
            $targetDataStr = $banner->getUrlBanner();

            $targetDataArr = explode('=', $targetDataStr);
            if (count($targetDataArr) >= 2) {
                $bannerDataItem['target'] = [
                    'key' => $targetDataArr[0],
                    'value' => $targetDataArr[1],
                ];
            }
            $returnData['data'][] = $bannerDataItem;

            return $returnData;

        } else {
            return $returnData;
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Sorry this banner does not exist')
            );
        }
    }

    /**
     * @return  configuiration data
     */
    public function parseConfiguiration()
    {
        $data = array();
        $store = $this->storeManager->getStore();
        $sliderManagementText = $this->scopeConfig->getValue('homemanagement_setting/general/management_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
//        $sliderManagementText = "slider(1);banner(3);product-slider(2);shop-by-brand;shop-by-cat(2)";
        $text = explode(';', $sliderManagementText);
        foreach ($text as $t) {
            if (preg_match("/(product-slider)/", $t)) {
                if (preg_match('/[\d]+/', $t, $categories)) {
                    $data[] = ['category' => $categories[0]];
                }
            } elseif (preg_match("/(slider)/", $t)) {
                if (preg_match('/[\d]+/', $t, $sliders)) {
                    $data[] = ['slider' => $sliders[0]];
                }
            } elseif (preg_match("/(shop-by-brand)/", $t)) {
                $data[] = ['shop_by_brand' => "shop_by_brand"];
            } elseif (preg_match("/(shop-by-cat)/", $t)) {
                if (preg_match('/[\d]+/', $t, $cats)) {
                    $data[] = ['shop_by_cat' => $cats[0]];
                } else {
                    $data[] = ['shop_by_cat' => 'shop_by_cat'];
                }
            } elseif (preg_match("/(banner)/", $t)) {
                if (preg_match('/[\d]+/', $t, $banners)) {
                    $data[] = ['banner' => $banners[0]];
                } else {
                    $data[] = ['banner' => 'banner'];
                }
            }
        }
        // var_dump ( $data );die;
        //        var_dump ( $text );die;
        return $data;
    }

    private function getShopByCatData($data)
    {
        $this->shopByCatCount =  $this->shopByCatCount + 1;
        if ($data = "shop_by_cat") {
            // default
        } else {
            // got id
        }
        $currentStore = $this->storeManager->getStore();
        $url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $limit=$this->scopeConfig->getValue('appconfigurations_setting/configs/homecategorieslimit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $currentStore);
//        $limit = $this->limit;
        $categories = $this->categotyCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_featured', 1)
            ->addAttributeToFilter('is_active', 1)
            ->setPageSize($limit);

        $returnData = [
            'component' => 'shop-by-cat',
            "id" =>  $this->shopByCatCount ,
            'name' => __("Shop by Category"),
            'data' => [],
        ];
        // var_dump( count($categories));die;
        foreach ($categories as $category) {
            $catData = [];
            $catData['id'] = $category->getId();
            $catData['name'] = $category->getName();
            $catData['icon'] = $category->getIcon();
            if (!empty($catData['icon'])) {
                $catData['icon'] = trim($url, '/') . '/catalog/category/' . $catData['icon'];
            }
            $catData['target'] = [
                'key' => 'cat',
                'value' => $category->getId(),
            ];

            $returnData['data'][] = $catData;
        }
        return $returnData;
    }

}
