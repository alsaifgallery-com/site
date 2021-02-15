<?php
namespace AlsaifGallery\Cart\Api;

interface CartManagementInterface {

    /**
     * Creates an empty cart and quote for a specified customer if customer does not have a cart yet.
     *
     * @param int $customerId The customer ID.
     * @return string[] new cart ID if customer did not have a cart or ID of the existing cart otherwise.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The empty cart and quote could not be created.
     */
    public function createEmptyCartForCustomer($customerId);

    /**
     * Creates an empty cart and quote for a specified customer if customer does not have a cart yet.
     * @param string[] sku list
     * @return string[] new cart ID if customer did not have a cart or ID of the existing cart otherwise.
     */
    public function getCrossSellProducts();

    /**
     *
     * @param string $sourceCart
     * @param string (optional) $destinationCart
     * @param int $customerId
     * @return array $types
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function mergeCart(
      $sourceCart,
      $destinationCart = null,
      $customerId
    );
}
