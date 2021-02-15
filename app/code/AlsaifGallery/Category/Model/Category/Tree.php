<?php
/**
 * Created by PhpStorm.
 * User: Dmytro Portenko
 * Date: 8/4/18
 * Time: 2:16 PM
 */

namespace AlsaifGallery\Category\Model\Category;

/**
 * Retrieve category data represented in tree structure
 */
class Tree
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Tree
     */
    protected $categoryTree;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollection;

    /**
     * @var \AlsaifGallery\Category\Api\Data\CategoryTreeInterfaceFactory
     */
    protected $treeFactory;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $flatState;

    protected $sliderAdapter;


    protected $catRepo;
    protected $bannerFactory;
    protected $bannerTargetFactory;
    protected $scopeConfig;

    protected $resourceMagento;
    protected $productResource;
    protected $productCollection;
    protected $catfilter;

    protected $brandFactory;
    protected $brandTargetFactory;
    protected $brandAdapter;
    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection
     * @param \Magento\Catalog\Api\Data\CategoryTreeInterfaceFactory $treeFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState,
//        \Magento\Catalog\Api\Data\CategoryTreeInterfaceFactory $treeFactory
        \AlsaifGallery\Category\Api\Data\CategoryTreeInterfaceFactory $treeFactory ,
        \AlsaifGallery\Category\Adapters\SliderInterface $sliderAdapter,
        \AlsaifGallery\Category\Adapters\BrandAdapterInterface $brandAdapter,
         \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Api\CategoryRepositoryInterface  $catRepo,
        \AlsaifGallery\Category\Api\Data\CategoryBannerInterfaceFactory $bannerFactory,
        \AlsaifGallery\Category\Api\Data\CategoryBannerTargetInterfaceFactory $bannerTargetFactory,
        \AlsaifGallery\Category\Api\Data\CategoryBrandInterfaceFactory $brandFactory,
        \AlsaifGallery\Category\Api\Data\CategoryBrandTargetInterfaceFactory $brandTargetFactory,
        \Magento\Framework\App\ResourceConnection $resourceMagento,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productResource ,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \AlsaifGallery\Filters\Api\CatfilterManagementInterface  $catfilter

    ) {
        $this->categoryTree = $categoryTree;
        $this->storeManager = $storeManager;
        $this->categoryCollection = $categoryCollection;
        $this->treeFactory = $treeFactory;
        $this->flatState = $flatState;
        $this->sliderAdapter = $sliderAdapter;
        $this->scopeConfig=$scopeConfig;
        $this->catRepo = $catRepo;

        $this->bannerTargetFactory = $bannerTargetFactory;
        $this->bannerFactory = $bannerFactory;

        $this->brandFactory = $brandFactory;
        $this->brandTargetFactory = $brandTargetFactory;
        $this->brandAdapter = $brandAdapter;

        $this->resourceMagento = $resourceMagento;
        $this->productResource = $productResource;
        $this->productCollection = $productCollection;

        $this->catfilter = $catfilter;

    }

    /**
     * @param \Magento\Catalog\Model\Category|null $category
     * @return Node|null
     */
    public function getRootNode($category = null)
    {
        if ($category !== null && $category->getId()) {
            return $this->getNode($category);
        }

        $store = $this->storeManager->getStore();
        $rootId = $store->getRootCategoryId();

        $tree = $this->categoryTree->load(null);
        $this->prepareCollection();
        $tree->addCollectionData($this->categoryCollection);
        $root = $tree->getNodeById($rootId);
        return $root;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return Node
     */
    protected function getNode(\Magento\Catalog\Model\Category $category)
    {
        $nodeId = $category->getId();
        $node = $this->categoryTree->loadNode($nodeId);
        $node->loadChildren();
        $this->prepareCollection();
        $this->categoryTree->addCollectionData($this->categoryCollection);
        return $node;
    }

    /**
     * @return void
     */
    protected function prepareCollection()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $this->categoryCollection
        ->addAttributeToFilter('include_in_menu', ['eq' => 1])
        ->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'is_active'
        )->addAttributeToSelect(
            'description'
        )->addAttributeToSelect(
            'icon'
        )->addAttributeToSelect(
                'is_featured'
        )->addAttributeToSelect(
                'include_in_menu'
        )->addAttributeToSelect(
                'brands'
        )->setProductStoreId(
            $storeId
        )->setLoadProductCount(
            true
        )->setStoreId(
            $storeId
        );

        if ($this->flatState->isAvailable()) {
            $this->categoryCollection->addAttributeToSelect('image');
            $this->categoryCollection->addAttributeToSelect('thumbnail');
            $this->categoryCollection->addAttributeToSelect('icon');
            $this->categoryCollection->addAttributeToSelect('is_featured');
            $this->categoryCollection->addAttributeToSelect('description');
            $this->categoryCollection->addAttributeToSelect('slider');
            $this->categoryCollection->addAttributeToSelect('include_in_menu');
            $this->categoryCollection->addAttributeToSelect('brands');
        } else {
            $this->categoryCollection->addAttributeToSelect('image', true);
            $this->categoryCollection->addAttributeToSelect('thumbnail', true);
            $this->categoryCollection->addAttributeToSelect('icon', true);
            $this->categoryCollection->addAttributeToSelect('is_featured', true);
            $this->categoryCollection->addAttributeToSelect('description', true);
            $this->categoryCollection->addAttributeToSelect('slider', true);
            $this->categoryCollection->addAttributeToSelect('include_in_menu', true);
            $this->categoryCollection->addAttributeToSelect('brands', true);
        }
    }

    /**
     * @param \Magento\Framework\Data\Tree\Node $node
     * @param int $depth
     * @param int $currentLevel
     * @return \AlsaifGallery\Category\Api\Data\CategoryTreeInterface
     */
    public function getTree($node, $depth = null, $currentLevel = 0)
    {
        //--
        $this->getBrandsInCategory();
        //--
        $store = $this->storeManager->getStore();
        $image=$this->scopeConfig->getValue('appconfigurations_setting/configs/category_image_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        /** @var \AlsaifGallery\Category\Api\Data\CategoryTreeInterface[] $children */
        $children = $this->getChildren($node, $depth, $currentLevel);
        /** @var \AlsaifGallery\Category\Api\Data\CategoryTreeInterface  $tree */
        $tree = $this->treeFactory->create();

        if( $node->getId() == 2 ){
             $caty  = $this->catRepo->get(2);
            $node = $caty;
        }

        $tree->setId($node->getId())
            ->setParentId($node->getParentId())
            ->setName($node->getName())
            ->setPosition($node->getPosition())
            ->setLevel($node->getLevel())
            ->setIsActive($node->getIsActive())
        // ->setIsFeatured($node->getIsFeatured())
            ->setProductCount($node->getProductCount())
            // ->setImage($node->getImage())
            // ->setDescription( $node->getDescription() )
            // ->setSlider( $node->getSlider() )
//            ->setfeature_image($node->getName())
             ->setIncludeInMenu($node->getIncludeInMenu())
            ->setChildrenData($children);

       $nodeThumbnailImage =$node->getThumbnail();
               if (!empty( $nodeThumbnailImage)){
                   $tree->setIcon($this->getThumbnailImageUrl($nodeThumbnailImage ));
               }

        $isFearured = $node->getIsFeatured();
        if (!is_null($isFearured)) {
            $tree->setIsFeatured($node->getIsFeatured());
        } else {
            $tree->setIsFeatured("0");
        }
        // $nodeIcon = $node->getIcon();
        // if (!empty($nodeIcon)) {
        //     $tree->setIcon($this->getIconUrl($nodeIcon));
        // }else{
        //     if(!empty($image)){
        //    $tree->setIcon($this->getIconUrlPlaceholder($image));
        //     }
        // }

        $tree->setSlider([]);
        $sliderId = $node->getSlider();
        // $tree->setSlider($sliderId);
        //         var_dump($sliderId);die;
        if (!is_null($sliderId)) {
            $banners = $this->sliderAdapter->getSliderBannerCollection($sliderId);
            if ($banners != false) {
                $returnData = [];
                foreach ($banners as $banner) {
                   $bannerDataItem = $this->bannerFactory->create();
                   $bannerDataItem->setBannerId( $banner->getBannerId() );
                   $bannerDataItem->setImage(  $banner->getImageUrl() );
                   $bannerDataItem->setPosition(   $banner->getPosition() );
                   $bannerDataItem->setName( $banner->getName());
                   $bannerDataItem->setTitle( $banner->getTitle());

                   $bannerDataItemTarget = $this->bannerTargetFactory->create();
                   $targetDataStr = $banner->getUrlBanner();
                    $targetDataArr = explode('=',$targetDataStr);
                    if( count($targetDataArr)>=2){
                      $bannerDataItemTarget->setKey( $targetDataArr[0] );
                      $bannerDataItemTarget->setValue( $targetDataArr[1] );
                      $bannerDataItem->setTarget( $bannerDataItemTarget );
                    }
                    $returnData[] = $bannerDataItem;
                }// end foreach
                 $tree->setSlider($returnData);
            }
        }


        $tree->setBrands([]);
         $brandsIds = $node->getBrands();
//        $brandsIds = "812,786,793";
        if (!is_null($brandsIds)) {
            $url = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            // 'mageplaza/brand/default'
            $defaultImage = $this->scopeConfig->getValue('shopbybrand/brandview/default_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());
            $brands = $this->getBrandsInCategory( $brandsIds  );
            // var_dump(count( $brands) );die;
            if ( !is_null($brands) && ( count($brands)>0)) {
                $returnData = [];
                foreach ($brands as $brand) {
                   // var_dump( $brand->getData() );die;
                   $brandDataItem = $this->brandFactory->create();
                   $brandDataItem->setBrandId( $brand->getBrandId() );
                   $brandDataItem->setBrandTitle( $brand->getPageTitle() );
                   $brandDataItem->setOptionId( $brand->getOptionId() );
                   $brandDataItem->setAttributeId( $brand->getAttributeId());
                   $brandDataItem->setUrlKey( $brand->getUrlKey());

                   // $brandDataItem->setImage(  $brand->getImage() );
                    $brandImage = $brand->getImage();
                    if (!empty( $brandImage )) {
                        $brandDataItem->setImage(trim($url, "/") . $brandImage );
                    } else {
                        // show default pic
                        if (empty($defaultImage)) {
                            $brandDataItem->setImage("");
                        } else {
                            $brandDataItem->setImage( trim($url, "/") . '/mageplaza/brand/' . $defaultImage);
                        }
                    }


                   $brandDataItemTarget = $this->brandTargetFactory->create();
                   $brandDataItemTarget->setKey( 'option_id' );
                   $brandDataItemTarget->setValue( $brand->getOptionId() );
                   $brandDataItem->setTarget( $brandDataItemTarget );

                   $returnData[] = $brandDataItem;
                 }// end foreach
                $tree->setBrands($returnData);
            }
    }

        return $tree;
    }

    /**
     * @param \Magento\Framework\Data\Tree\Node $node
     * @param int $depth
     * @param int $currentLevel
     * @return \AlsaifGallery\Category\Api\Data\CategoryTreeInterface[]|[]
     */
    protected function getChildren($node, $depth, $currentLevel)
    {
        if ($node->hasChildren()) {
            $children = [];
            foreach ($node->getChildren() as $child) {
                if ($depth !== null && $depth <= $currentLevel) {
                    break;
                }
                $children[] = $this->getTree($child, $depth, $currentLevel + 1);
            }
            return $children;
        }
        return [];
    }

    public function getFeaturedImageUrl($catFeatureImage)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . "catalog/category/" . $catFeatureImage;
    }

    public function getThumbnailImageUrl($catThumbnailImage)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . "catalog/category/" . $catThumbnailImage;
    }
    public function getIconUrl($image)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . "catalog/category/" . $image;
    }
    public function getIconUrlPlaceholder($image){
     $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
     return $mediaUrl . "blog/post4/" . $image;
    }

    private function getBrandsInCategory($brandIds=null)
    {
        $retData= [];

        if($brandIds==null){
            return $retData;
        }
        $brands  = $this->brandAdapter->getBrandsByIds( $brandIds );
        // var_dump($brands);die;
        if( count( $brands )>0){
            return $brands;
        }

        return  $retData;


        // $connection = $this->resourceMagento->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

//        $result = $connection->fetchAll("SELECT DISTINCT cat.value FROM catalog_product_entity_varchar as cat
//            JOIN eav_attribute as eav ON eav.attribute_id=cat.attribute_id
//            WHERE eav.attribute_code='manufacturer'");
//        $result = $connection->fetchAll("SELECT DISTINCT  * FROM catalog_product_entity_int as cat
//            JOIN eav_attribute as eav ON eav.attribute_id=cat.attribute_id
//            WHERE eav.attribute_code='manufacturer' limit 10");
//
        // var_dump( $result );die;

//        $col = $this->productCollection->addFieldToSelect("manufacturer");
//        $col = $this->productCollection->addFieldToSelect("manufacturer");
//        $col->getSelect()->joinLeft(
//                ['attr' => $col->getTable('catalog_product_entity_int')],
//                'e.entity_id = attr.entity_id',
//                "*"
//                );
//        $col->getSelect()->joinLeft(
//                ['eav_attr' => $this->getTable('eav_attribute')],
//                'main_table.entity_id = eav_attr.status_id and label.store_id = '
//                . (int)$storeId,
//                ['status_name' => 'label.label']
//
//                );
        // $col->getSelect()->group("e.manufacturer");

        // var_dump( count ($col) );die;
        // var_dump( $col->getFirstItem()->getData() );die;
        // var_dump(  $col->getSelect()->__tostring()  );die;
        // $catId=2;

        // $res = $this->catfilter->getCatfilterById($catId);
        // var_dump($res);die;
    }

}
