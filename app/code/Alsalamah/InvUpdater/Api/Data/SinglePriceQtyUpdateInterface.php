<?php
namespace Alsalamah\InvUpdater\Api\Data;
 
/**
 * Interface for tracking cart product updates.
 */
interface SinglePriceQtyUpdateInterface {
    /**
     * Gets the sku.
     *
     * @api
     * @return string
     */
    public function getSku();
 
    /**
     * Sets the sku.
     *
     * @api
     * @param int $sku
     * @return void
     */
    public function setSku($sku);
 
    /**
     * Gets the price.
     *
     * @api
     * @return float
     */
    public function getPrice();
 
    /**
     * Sets the quantity.
     *
     * @api
     * @param float $price
     * @return void
     */
    public function setPrice($price);

    /**
     * Gets the quantity.
     *
     * @api
     * @return float
     */
    public function getQty();
 
    /**
     * Sets the quantity.
     *
     * @api
     * @param float $qty
     * @return void
     */
    public function setQty($qty);    
}
