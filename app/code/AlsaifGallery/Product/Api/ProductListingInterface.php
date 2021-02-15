<?php
namespace AlsaifGallery\Product\Api;


/**
 * @api
 * @since 100.0.2
 */
interface ProductListingInterface
{
    /**
     * Get product list
     *
     * @param \AlsaifGallery\Product\Api\Data\FilterItemInterface[] $filterList
     * @param int $currentPage
     * @param int $pageSize
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    // \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    public function getList( array $filterList, $currentPage,$pageSize);   
//    public function getList( \AlsaifGallery\Product\Api\Data\FilterListInterface $filterList );   

}
