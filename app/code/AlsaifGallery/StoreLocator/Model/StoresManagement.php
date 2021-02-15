<?php

namespace AlsaifGallery\StoreLocator\Model;

use Magento\Framework\Exception\LocalizedException;
use Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory;
use Amasty\Storelocator\Model\ResourceModel\Location\Collection as LocationCollection;
use Amasty\Storelocator\Model\Location as LocationModel;
use Amasty\Storelocator\Block\Location;
use Amasty\Storelocator\Model\ConfigProvider;

class StoresManagement implements \AlsaifGallery\StoreLocator\Api\StoresManagementInterface
{

    /**
     * @var CollectionFactory
     */
    private $locationCollectionFactory;

    /**
     * @var Location
     */
    private $location;

    /**
     * @var LocationModel
     */
    private $locationModel;

    /**
     * @var LocationCollection
     */
    private $locationCollection;

    /**
     * @var ConfigProvider
     */
    public $configProvider;

    public function __construct(
        CollectionFactory $locationCollectionFactory,
        Location $locations,
        LocationModel $locationModel,
        ConfigProvider $configProvider
    ) {
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->location = $locations;
        $this->locationModel = $locationModel;
        $this->configProvider = $configProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllStores()
    {
      if (!$this->locationCollection) {
          $this->locationCollection = $this->locationCollectionFactory->create();
          $this->locationCollection->applyDefaultFilters();
          $this->locationCollection->joinScheduleTable();
      }
      if ($attributesData = $this->location->prepareWidgetAttributes()) {
          $this->locationCollection->clear();
          $this->locationCollection->applyAttributeFilters($attributesData);
      }
      $this->locationCollection->setCurPage(1);
      $this->locationCollection->setPageSize($this->configProvider->getPaginationLimit());

      $dataArray = [];
      foreach ($this->locationCollection as $location) {
          /** @var LocationModel $location */
          // $location->setRating($this->location->getRatingHtml($location));
          $location->setTemplatesHtml();
          $data = [
              "store_id"        => $location->getId(),
              "name"            => $location->getName(),
              "Phone"           => $location->getPhone(),
              "city"         => $location->getCity(),
              "address"         => $location->getAddress(),
              "latitude"        => $location->getLat(),
              "longitude"       => $location->getLng()
          ];
          $dataArray[] = $data;
      }

       return $dataArray;
    }

}
