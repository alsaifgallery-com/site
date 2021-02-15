<?php


namespace AlsaifGallery\Product\Api\Data;

interface FilterListInterface
{
    const FILTER_LIST='filters_list';
    const PAGE_SIZE='page_size';
    const CURRUNT_PAGE='currunt_page';

    
    /**
     * get  Filters
     
     *  @return  \AlsaifGallery\Product\Api\Data\FilterItemInterface[]
     */
    public function getFilterList();
    /**
     * set  filters
     * @param \AlsaifGallery\Product\Api\Data\FilterItemInterface[] $filterList
     * @return  \AlsaifGallery\Product\Api\Data\SpecificationsInterface
     */
    public function setFilterList($filterList);
        
        
    /**
     * GET current page
     
     *  @return  int
     */
    public function getCurrentPage();
    /**
     * set current page
     *  @param int $currentPage
     *  @return  \AlsaifGallery\Product\Api\Data\SpecificationsInterface
     */
    public function setCurrentPage($currentPage);
    /**
     * get  page size
     
     *  @return  int
     */
    public function getPageSize();
    /**
     * set page size 
     * @param int $pageSize
     *  @return  \AlsaifGallery\Product\Api\Data\SpecificationsInterface
     */
    public function setPageSize($pageSize);
}
