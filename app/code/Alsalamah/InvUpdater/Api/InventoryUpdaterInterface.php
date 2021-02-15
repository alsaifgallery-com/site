<?php
namespace Alsalamah\InvUpdater\Api;
 
interface InventoryUpdaterInterface
{
    /**
    * bulk update price
    * @param \Alsalamah\InvUpdater\Api\Data\MassPriceQtyUpdateInterface $massPriceUpdate
    * @return \Alsalamah\InvUpdater\Api\Data\MassItemUpdateResultInterface    
    */
    public function updatePrice(\Alsalamah\InvUpdater\Api\Data\MassPriceQtyUpdateInterface $massPriceUpdate);

    /**
    * bulk update qty
    * @param \Alsalamah\InvUpdater\Api\Data\MassPriceQtyUpdateInterface $massQtyUpdate
    * @return \Alsalamah\InvUpdater\Api\Data\MassItemUpdateResultInterface   
    */
    public function updateQty(\Alsalamah\InvUpdater\Api\Data\MassPriceQtyUpdateInterface $massQtyUpdate);
}
