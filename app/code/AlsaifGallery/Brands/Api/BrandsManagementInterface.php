<?php


namespace AlsaifGallery\Brands\Api;

interface BrandsManagementInterface
{

    /**
     * GET for Brands api
     * @param int $optionId
     * @return string[]
     */
    public function geProductsBrand($optionId);
    
    /**
     * GET for Brands api
     * 
     * @return string[]
     */
    
    public function getBrandsList();
}
