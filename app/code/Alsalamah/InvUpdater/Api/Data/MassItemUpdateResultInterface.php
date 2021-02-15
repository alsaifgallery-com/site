<?php
namespace Alsalamah\InvUpdater\Api\Data;
 
/**
 * Interface for tracking the result of item qty/price updates
 */
interface MassItemUpdateResultInterface {
    /**
     * Gets list of SingleItemUpdateResult
     *
     * @api
     * @return \Alsalamah\InvUpdater\Api\Data\SingleItemUpdateResultInterface[]|null
     */
    public function getList();
 
    /**
     * Sets the list of SingleItemUpdateResult
     *
     * @api
     * @param \Alsalamah\InvUpdater\Api\Data\SingleItemUpdateResultInterface[] $list
     * @return void
     */
    public function setList(array $list = null);
}
