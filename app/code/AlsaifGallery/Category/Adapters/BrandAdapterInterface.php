<?php
namespace AlsaifGallery\Category\Adapters;

interface BrandAdapterInterface {
    public function getBrands();
    public function getBrandsByIds( $brandIds );
    public function getProductBrand($productId);
    public function getBrandAttributeCode();
    
}
