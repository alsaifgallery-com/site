<?php


namespace AlsaifGallery\WishList\Api;
use \WebbyTroops\WishlistAPI\Api\Data\ResponseInterface;
interface ReverseWishListManagementInterface
{

    /**
     * POST for ReverseWishList api
     * @param int $customerId
     * @param string $sku
     * @return string[]|boolean
     * 
     */
    public function reverseWishList($customerId,$sku);
}
