<?php


namespace AlsaifGallery\HomeManager\Api;
use Magento\Catalog\Api\ProductRepositoryInterface;
interface HomeSliderManagementInterface
{

    /**
     * GET for HomeManager api
     
     *  @return  string[]
     */
    public function getHomeSlider();
}
