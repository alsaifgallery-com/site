<?php


namespace AlsaifGallery\DailyDeals\Model;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Search\Api\SearchInterface;
class DailyDealsManagement implements \AlsaifGallery\DailyDeals\Api\DailyDealsManagementInterface
{
    public $dealsCollection;
    
    public $product;
    
    public $category;
    
    public $searchCriteriaBuilder;
    
    public $categoryCollectionFactory;
    
    public $categoryLink;
    
    public $dealFactory;
    
    public $data;
    
    public $searchBuilder;
    public function __construct(
            \Mageplaza\DailyDeal\Model\ResourceModel\Deal\CollectionFactory $dealsCollection,
            \Magento\Catalog\Api\ProductRepositoryInterface $product,
            \Magento\Catalog\Api\CategoryRepositoryInterface $category,
            \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
            \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
            \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLink,
            \Mageplaza\DailyDeal\Model\DealFactory $dealFactory,
            \Mageplaza\DailyDeal\Helper\Data $data,
            \Magento\Framework\Api\SearchCriteriaBuilder $searchBuilder
            )
    {
        $this->dealsCollection=$dealsCollection;
        $this->product=$product;
        $this->category=$category;
        $this->searchCriteriaBuilder=$searchCriteriaBuilder;
        $this->categoryCollectionFactory=$categoryCollectionFactory;
        $this->categoryLink=$categoryLink;
        $this->dealFactory=$dealFactory;
        $this->data=$data;
        $this->searchBuilder=$searchBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductsAndCategorieseDailyDeals()
    {   
        
        $products=$returnesData=$categoriesIds=[];              
        $deals= $this->getCheckedDealsCollection();
        foreach ($deals as $deal){
            $productObject=$this->product->getById($deal['product_id']);
//            $productData['id']=$productObject->getId();
//            $productData['sku']=$productObject->getSku();
//            $productData['name']=$productObject->getName();
//            $productData['price']=$productObject->getPrice();
//            $productData['type']=$productObject->getTypeId();
//            $productData['status']=$productObject->getStatus();
//            $productData['visibility']=$productObject->getVisibility();
//            $productData['created_at']=$productObject->getCreatedAt();
//            $productData['updated_at']=$productObject->getUpdatedAt();
           
            array_push($products,$productObject->toArray()) ; 
           $categoriesIds= array_unique(array_merge($categoriesIds, $productObject->getCategoryIds()));
        }       
        $returnesData['products']=$products;
        $returnesData['categories']= $this->getAllCategoriesDeals($categoriesIds);
        return array($returnesData);
     
    }
    public function getAllCategoriesDeals($categoriesIds,$page,$limit){
       $categories=[];
       $categoryCollection=$this->categoryCollectionFactory->create();
       $categoryCollection->addAttributeToFilter('entity_id',$categoriesIds)
                           ->addAttributeToFilter('level',['gt' => 2])
                           ->addAttributeToSelect('name')
                           ->setPage($page, $limit);
                           
              foreach ($categoryCollection as $category){
                $categoryData['id']=$category->getId();
                $categoryData['name']=$category->getName();
                $categoryData['parent_id']=$category->getParentId();
                $categoryData['path']=$category->getPath();
                $categoryData['level']=$category->getLevel();
                $categoryData['position']=$category->getPosition();
                $categoryData['created_at']=$category->getCreatedAt();
                $categoryData['updated_at']=$category->getUpdatedAt();
                array_push($categories,$categoryData);
                       
            } 
            return $categories;
    }
    
     /**
     * {@inheritdoc}
      * 
     */
    public function getProductsDeal($page,$limit){
        $products = $productIds = [];
        $deals = $this->getCheckedDealsCollection();
        foreach ($deals as $deal) {
            array_push($productIds, $deal['product_id']);
        }
//        $this->searchBuilder->setCurrentPage(1);
        $this->searchBuilder->setPageSize($limit);
        $this->searchBuilder->setCurrentPage($page);
        $searchCriteria = $this->searchBuilder->addFilter('entity_id', $productIds, 'in')->create();
        $resault = $this->product->getList($searchCriteria);     
        return $resault;
    }
    
    
     /**
     * {@inheritdoc}
     * 
     */
    public function getProductsDealsOfCategory($categoryId,$page,$limit){
        
        $products = $productIds = [];
        $deals = $this->getCheckedDealsCollection();
        foreach ($deals as $deal) {
            $productObject = $this->product->getById($deal['product_id']);
            if (in_array($categoryId, $productObject->getCategoryIds())) {
                array_push($productIds, $deal['product_id']);
            }
        }
        $this->searchBuilder->setCurrentPage($page);
        $this->searchBuilder->setPageSize($limit);
        $searchCriteria = $this->searchBuilder->addFilter('entity_id', $productIds, 'in')->create();
        $resault = $this->product->getList($searchCriteria);     
//         foreach ($resault->getItems() as $product) {
//             var_dump($product);
//             die;
//             array_push($products,$product);
//        }

        return $resault;

    }
     /**
     * {@inheritdoc}
      * 
     */
    
    public function getCategoriesDeal($page,$limit) {
      $categoriesIds=[];          
        $deals= $this->getCheckedDealsCollection();
        foreach ($deals as $deal){
            $productObject=$this->product->getById($deal['product_id']);
//            array_push($products,$productObject->toArray()) ; 
           $categoriesIds= array_unique(array_merge($categoriesIds, $productObject->getCategoryIds()));
        }       
       
       
        return  $this->getAllCategoriesDeals($categoriesIds,$page,$limit);
    
    }

    public function getCheckedDealsCollection(){
     $deals=[];   
     $dealsCollection= $this->dealsCollection->create()->getData();
        foreach($dealsCollection as $deal){
            if($this->data->checkStatusDeal($deal['product_id'])){
                if(!$this->data->checkEndedDeal($deal['product_id'])){
                    array_push($deals,$deal);
                }
                
            }
        }
        return $deals;
    }
     /**
     * {@inheritdoc}
      * 
     */
    public function getProductDeal($productId){
    $checked=false;    
    $deal= $this->data->getProductDeal($productId);
    if($deal){
    $checked=$this->data->checkDealProduct($productId);
    }
    if($checked){
     return $deal;   
    }
    
    }
}
