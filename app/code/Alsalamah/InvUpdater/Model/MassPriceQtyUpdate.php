<?php
namespace Alsalamah\InvUpdater\Model;
 
use \Alsalamah\InvUpdater\Api\Data\MassPriceQtyUpdateInterface;
 
/**
 * Model that contains updated cart information.
 */
class MassPriceQtyUpdate implements MassPriceQtyUpdateInterface {
 
    /**
     * list
     * @var \Alsalamah\InvUpdater\Api\Data\SinglePriceQtyUpdateInterface[]
     */
    protected $list;
  
    /**
     * Gets list of SinglePriceQtyUpdate
     *
     * @api
     * @return \Alsalamah\InvUpdater\Api\Data\SinglePriceQtyUpdateInterface[]|null
     */
    public function getList() {
        return $this->list;
    }
 
    /**
     * Sets the list of SinglePriceQtyUpdate
     *
     * @api
     * @param \Alsalamah\InvUpdater\Api\Data\SinglePriceQtyUpdateInterface[] $list
     * @return void
     */
    public function setList(array $list = null) {
        $this->list = $list;
    }
 
}
