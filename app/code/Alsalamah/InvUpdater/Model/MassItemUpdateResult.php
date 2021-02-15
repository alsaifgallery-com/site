<?php
namespace Alsalamah\InvUpdater\Model;
 
use \Alsalamah\InvUpdater\Api\Data\MassItemUpdateResultInterface;
 
/**
 * Model that contains updated cart information.
 */
class MassItemUpdateResult implements MassItemUpdateResultInterface {
 
    /**
     * list
     * @var \Alsalamah\InvUpdater\Api\Data\SingleItemUpdateResultInterface[]
     */
    protected $list;
  
    /**
     * Gets list of SingleItemUpdateResult
     *
     * @api
     * @return \Alsalamah\InvUpdater\Api\Data\SingleItemUpdateResultInterface[]|null
     */
    public function getList() {
        return $this->list;
    }
 
    /**
     * Sets the list of SingleItemUpdateResult
     *
     * @api
     * @param \Alsalamah\InvUpdater\Api\Data\SingleItemUpdateResultInterface[] $list
     * @return void
     */
    public function setList(array $list = null) {
        $this->list = $list;
    }
 
}
