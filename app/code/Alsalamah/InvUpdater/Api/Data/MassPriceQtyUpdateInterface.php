<?php
namespace Alsalamah\InvUpdater\Api\Data;
 
/**
 * Interface for tracking cart product updates.
 */
interface MassPriceQtyUpdateInterface {
    /**
     * Gets list of SinglePriceQtyUpdate
     *
     * @api
     * @return \Alsalamah\InvUpdater\Api\Data\SinglePriceQtyUpdateInterface[]|null
     */
    public function getList();
 
    /**
     * Sets the list of SinglePriceQtyUpdate
     *
     * @api
     * @param \Alsalamah\InvUpdater\Api\Data\SinglePriceQtyUpdateInterface[] $list
     * @return void
     */
    public function setList(array $list = null);
}
