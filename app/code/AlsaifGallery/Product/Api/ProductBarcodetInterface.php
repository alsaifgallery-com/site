<?php
namespace AlsaifGallery\Product\Api;


/**
 * @api
 * @since 100.0.2
 */
interface ProductBarcodetInterface
{

    /**
     * Get info about product by product Barcode
     *
     * @param string $barcode
     * @param bool $editMode
     * @param int|null $storeId
     * @param bool $forceReload
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct($barcode ,$editMode = false, $storeId = null, $forceReload = false);
    
    

}
