<?php
namespace Alsalamah\InvUpdater\Model;
 
use \Alsalamah\InvUpdater\Api\Data\SinglePriceQtyUpdateInterface;
 
/**
 * Model that contains updated cart information.
 */
class SinglePriceQtyUpdate implements SinglePriceQtyUpdateInterface {
 
    /**
     * sku
     * @var string
     */
    protected $sku;
 
    /**
     * price
     * @var float
     */
    protected $price;

    /**
     * qty
     * @var float
     */
    protected $qty;    
 
    /**
     * Gets the sku.
     *
     * @api
     * @return string
     */
    public function getSku() {
        return $this->sku;
    }
 
    /**
     * Sets the sku.
     *
     * @api
     * @param int $sku
     */
    public function setSku($sku) {
        $this->sku = $sku;
    }
 
    /**
     * Gets the price.
     *
     * @api
     * @return float
     */
    public function getPrice() {
        return $this->price;
    }
 
    /**
     * Sets the quantity.
     *
     * @api
     * @param float $price
     * @return void
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * Gets the quantity.
     *
     * @api
     * @return string
     */
    public function getQty() {
        return $this->qty;
    }
 
    /**
     * Sets the quantity.
     *
     * @api
     * @param int $qty
     * @return void
     */
    public function setQty($qty) {
        $this->qty = $qty;
    }    
}
