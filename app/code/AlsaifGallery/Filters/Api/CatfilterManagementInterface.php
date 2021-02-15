<?php


namespace AlsaifGallery\Filters\Api;

interface CatfilterManagementInterface
{

    /**
     * GET for catfilter api

     * @return string[]
     */
    public function getCatfilter();
 
    /**
     * GET for catfilter by id
     * @param string $catId
     * @return string[]
     */
//    public function getCatfilterById($catId);
}
