<?php
namespace AlsaifGallery\Product\Adapters;

interface BrandAdapterInterface {
    public function getProductBrand($productId);
    public function getBrandAttributeCode();
}
