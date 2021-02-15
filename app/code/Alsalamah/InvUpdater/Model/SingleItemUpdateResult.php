<?php
namespace Alsalamah\InvUpdater\Model;
 
use \Alsalamah\InvUpdater\Api\Data\SingleItemUpdateResultInterface;
 
/**
 * Model that contains updated cart information.
 */
class SingleItemUpdateResult implements SingleItemUpdateResultInterface {
 
    /**
     * sku
     * @var string
     */
    protected $sku;

    /**
     * success
     * @var int
     */
    protected $success;
    
    /**
     * error
     * @var string
     */
    protected $error;    


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
     * Gets the success value.
     *
     * @api
     * @return int
     */
    public function getSuccess() {
      return $this->success;
    }
 
    /**
     * Sets the success value.
     *
     * @api
     * @param int $success
     * @return void
     */
    public function setSuccess($success) {
      $this->success = $success;
    }

    /**
     * Gets the error message.
     *
     * @api
     * @return string
     */
    public function getError() {
      return $this->error;
    }
 
    /**
     * Sets the error message.
     *
     * @api
     * @param string $error
     * @return void
     */
    public function setError($error)  {
      $this->error = $error;
    }  

}