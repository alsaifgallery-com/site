<?php
namespace Alsalamah\InvUpdater\Api\Data;
 
/**
 * Interface for tracking results of price/qty update for a single item.
 */
interface SingleItemUpdateResultInterface {
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
     * Gets the success value.
     *
     * @api
     * @return int
     */
    public function getSuccess();
 
    /**
     * Sets the success value.
     *
     * @api
     * @param int $success
     * @return void
     */
    public function setSuccess($success);

    /**
     * Gets the error message.
     *
     * @api
     * @return string
     */
    public function getError();
 
    /**
     * Sets the error message.
     *
     * @api
     * @param string $error
     * @return void
     */
    public function setError($error);    
}
