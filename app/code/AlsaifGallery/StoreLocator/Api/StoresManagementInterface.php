<?php


namespace AlsaifGallery\StoreLocator\Api;

use Amasty\Storelocator\Model\Location as LocationCollection;

interface StoresManagementInterface
{


  /**
   * Get reviews of the product
   * @return string[]|bool
   */
    public function getAllStores();
}
