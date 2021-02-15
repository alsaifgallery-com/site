<?php
namespace AlsaifGallery\HomeManager\Adapters;

interface BrandAdapterInterface {
    public function getBrands();
    public function getProductBrand($productId);
    public function getBrandAttributeCode();
    
}
