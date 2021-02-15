<?php
namespace AlsaifGallery\Cart\Api;

interface CartItemInterface {
    
    /**
     * Removes the specified item from the specified cart.
     *
     * @param int $cartId The cart ID.
     * @param int $itemId The item ID of the item to be removed.
     * @return string[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified item or cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The item could not be removed.
     */
    public function deleteById($cartId, $itemId);
}