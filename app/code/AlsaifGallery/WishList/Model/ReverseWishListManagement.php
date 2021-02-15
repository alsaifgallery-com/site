<?php

namespace AlsaifGallery\WishList\Model;

class ReverseWishListManagement implements \AlsaifGallery\WishList\Api\ReverseWishListManagementInterface
{
    public $wishListRepo;

    public $productRepository;

    public function __construct(
        \WebbyTroops\WishlistAPI\Api\WishlistRepositoryInterface $wishListRepo,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository) {
        $this->wishListRepo = $wishListRepo;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseWishList($customerId, $sku) {

        $wishLists = $this->wishListRepo->getWishlist($customerId);

        foreach ($wishLists->getWishlistItems() as $item) {
            if ($item->getProduct()->getSku() == $sku) {
                $this->wishListRepo->removeWishlistItem($customerId, $item->getItemId());
                return [['message' => __('Product Remove Sussfully from the Wishlist')->render(), 'status' => true]];
            }
        }
        $this->wishListRepo->addWishlistItem($customerId, $sku);

        return [['message' => __('Product Added Sussfully to the Wishlist')->render(), 'status' => true]];
    }

}
