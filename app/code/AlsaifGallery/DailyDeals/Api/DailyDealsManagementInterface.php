<?php


namespace AlsaifGallery\DailyDeals\Api;

interface DailyDealsManagementInterface
{

    /**
     * GET for DailyDeals api
     * 
     * @return string[]
     */
    public function getProductsAndCategorieseDailyDeals();
    
    
    /**
     * GET for DailyDeals api
     * @param int $categoryId
     * @param int $page
     * @param int $limit
     * @return Magento\Catalog\Api\Data\ProductSearchResultsInterface;
     */
    public function getProductsDealsOfCategory($categoryId,$page,$limit);
    
    /**
     * GET for DailyDeals api
     * @param int $productId
     * @return  DataObject
     */
    public function getProductDeal($productId);
    
    /**
     * GET for DailyDeals api
     * @param int $page
     * @param int $limit
     * @return  Magento\Catalog\Api\Data\ProductSearchResultsInterface;
     */ 
    public function getProductsDeal($page,$limit);
    
    
     /**
     * GET for DailyDeals api
     * @param int $page
     * @param int $limit 
     * @return string[]
     */ 
    public function getCategoriesDeal($page,$limit);
}
