<?php

namespace AlsaifGallery\Product\Model;

class ProductListingMangement implements \AlsaifGallery\Product\Api\ProductListingInterface
{
    protected  $productRepo;
    protected  $searchBuilder;


    public function __construct(
       \Magento\Catalog\Api\ProductRepositoryInterface $productRepo,
       \Magento\Framework\Api\SearchCriteriaBuilder $searchBuilder
    ) {
        $this->productRepo = $productRepo;
        $this->searchBuilder = $searchBuilder;
    }

    /**
     * {@inheritdoc}
     */
    
    public function getList( array $filterList, $currentPage=1,$pageSize=10) {
        $replace['cat'] = "category_id";
        $validFilters = [];
        // echo count($filterList);die;
        foreach( $filterList as $filter){
            if( $filter instanceof \AlsaifGallery\Product\Api\Data\FilterItemInterface ){
               $key="";
               switch ( $filter->getField() ){
                   case "cat":
                       $key="category_id";
                       break;
                   default:
                       $key= $filter->getField();
                       break;
               }
               $validFilters[] =  $filter;
               $this->searchBuilder->addFilter( $key , $filter->getValue());
            }
        }
        
        $this->searchBuilder->setCurrentPage( $currentPage );
        $this->searchBuilder->setPageSize( $pageSize );
        // $this->searchBuilder->addFilter('category_id', $id);
        $searchCriteria = $this->searchBuilder->create();

        return $this->productRepo->getList($searchCriteria);
        
    }
    /**
     * {@inheritdoc}
     */
    
    public function getListOld( \AlsaifGallery\Product\Api\Data\FilterListInterface $filterList) {
        
        
        $this->searchBuilder->setCurrentPage(1);
        $this->searchBuilder->setPageSize(9999);
        // $this->searchBuilder->addFilter('category_id', $id);
        $searchCriteria = $this->searchBuilder->create();

        return $this->productRepo->getList($searchCriteria);
        
    }

}
