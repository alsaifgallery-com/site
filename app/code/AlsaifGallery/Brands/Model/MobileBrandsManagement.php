<?php

namespace AlsaifGallery\Brands\Model;

class BrandsManagement implements \AlsaifGallery\Brands\Api\BrandsManagementInterface
{

    public $product;
    public $searchCriteriaBuilder;
    public $scopeConfig;
    public $storeManager;
    public $brand;
    public $collectionFactory;
    public $productFactory;
    public $data;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $product,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageplaza\Shopbybrand\Model\BrandFactory $brand,
        \Mageplaza\Shopbybrand\Model\ResourceModel\Brand\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productFactory,
        \Mageplaza\Shopbybrand\Helper\Data $data
    ) {
        $this->product = $product;
        $this->scopeConfig = $scopeConfig;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->brand = $brand;
        $this->collectionFactory = $collectionFactory;
        $this->productFactory = $productFactory;
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function geProductsBrand($optionId)
    {
        $poductReource = $this->productFactory->create();
        $brandAttribute = $this->scopeConfig->getValue('shopbybrand/general/attribute', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId());
        $attribute = $poductReource->getAttribute($brandAttribute);
        $mediaURL = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\Url::URL_TYPE_MEDIA);
        $brandFacory = $this->brand->create();
        $brandObject = $brandFacory->loadByOption($optionId);
        $brand['option_id'] = $brandObject['option_id'];
        if ($attribute->usesSource()) {
            $brand['brand_name'] = $attribute->getSource()->getOptionText($brandObject['option_id']);
        }
        $brand['brand_title'] = $brandObject['page_title'];
        $brand['url_key'] = $brandObject['url_key'];
        $brand['description'] = $brandObject['description'];
        $brand['attribute_id'] = $brandObject['attribute_id'];
        $brand['target'] = [
            "key" => 'option_id',
            "value" => $brandObject['option_id'],
        ];
        $brand['base_url'] = $mediaURL;
        $defaultImage = $this->scopeConfig->getValue(
            'shopbybrand/brandview/default_image',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId());

        if (empty($brandObject['image'])) {
            $brand['brand_image'] = '/mageplaza/brand/' . $defaultImage;
        } elseif (empty($defaultImage)) {
            $brand['brand_image'] = "";
        } else {
            $brand['brand_image'] = '' . $brandObject['image'];
        }
        $brand['data'] = $this->getProducts($optionId);
        return array($brand);
    }

    public function getProducts($optionId)
    {
        $products = [];
        $currentStore = $this->storeManager->getStore();
        $url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $data['base_url'] = $url . '/catalog/product';
        $brandAttribute = $this->scopeConfig->getValue('shopbybrand/general/attribute', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId());
        $searchCriteria = $this->searchCriteriaBuilder->setPageSize(5)
            ->setCurrentPage(1)->addFilter($brandAttribute, $optionId)->create();
        $items = $this->product->getList($searchCriteria)->getItems();
        foreach ($items as $item) {
            $data['sku'] = $item->getSku();
            try {
                $data['stock'] = $this->stockState->verifyStock($item->getId(), $currentStore->getId());
            } catch (\Exception $e) {
                // nothing
            }
            $data['name'] = $item->getName();
            $data['status'] = $item->getStatus();

            $onsaleEnabled = $item->getOnsaleEnabled();
            if (is_null($onsaleEnabled)) {
                $data['onsale_enabled'] = "0";
            } else {
                $data['onsale_enabled'] = $onsaleEnabled;
            }

            $data['type'] = $item->getTypeId();
            if (in_array($data['type'], ['simple', 'downloadable', 'virtual'])) {
                $price = $item->getPrice();
                $data['price'] = $price;
                $specialPrice = $item->getSpecialPrice();
                if ($price > 0) {
                    if (isset($specialPrice) && $specialPrice >= 1) {
                        $data['sale_price'] = $specialPrice;
                        $precent = ($specialPrice / $price) * 100;
                        $data['sale_percent'] = 100 - (int) $precent;
                    }
                }
            }
            $data['is_wishlist_product'] = $item->getExtensionAttributes()->getIsWishlistProduct();
            $data['is_cart_product'] = $item->getExtensionAttributes()->getIsCartProduct();
            $data['thumbnail'] = $item->getExtensionAttributes()->getThumbnail();

            $data['image'] = $url . '/catalog/product' . $item->getCustomAttribute('image')->getValue();
            array_push($products, $data);
        }
        return $products;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandsList()
    {
        $returnedData = [];
        $brandCollection = $this->brand->create()->getBrandCollection();
        $defaultImage = $this->scopeConfig->getValue(
            'shopbybrand/brandview/default_image',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId());

        $brands = $brandCollection->getData();
        $currentStore = $this->storeManager->getStore();
        $url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        foreach ($brands as $brand) {

            $data['option_id'] = $brand['option_id'];
            $data['brand_name'] = $brand['value'];
            $data['base_url'] = $url;

            if (empty($brand['image'])) {
                $data['brand_image'] = '/mageplaza/brand/' . $defaultImage;
            } elseif (empty($defaultImage)) {
                $data['brand_image'] = "";
            } else {
                $data['brand_image'] = $brand['image'];
            }
            $data['target'] = [
                "key" => 'option_id',
                "value" => $brand['option_id'],
            ];
            array_push($returnedData, $data);
        }

        return $returnedData;
    }

}
