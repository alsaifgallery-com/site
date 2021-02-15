<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\StoreLocator\Controller\Index;

use Magento\Framework\Exception\LocalizedException;
use Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory;
use Amasty\Storelocator\Model\ResourceModel\Location\Collection as LocationCollection;
use Amasty\Storelocator\Model\Location as LocationModel;
use Amasty\Storelocator\Block\Location;
use Amasty\Storelocator\Model\ConfigProvider;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

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

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CollectionFactory $locationCollectionFactory,
        Location $locations,
        LocationModel $locationModel,
        ConfigProvider $configProvider
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->location = $locations;
        $this->locationModel = $locationModel;
        $this->configProvider = $configProvider;
        parent::__construct($context);
    }

    /**
     * Get reviews of the product
     * @return string[]|bool
     */
    public function execute()
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
          $location->setRating($this->location->getRatingHtml($location));
          $location->setTemplatesHtml();
          $data = [
              "store_id"        => $location->getId(),
              "name"            => $location->getName(),
              "Phone"           => $location->getPhone(),
              "address"         => $location->getAddress(),
              "latitude"        => $location->getLat(),
              "longitude"       => $location->getLng()
          ];
          $dataArray[] = $data;
      }

       return $dataArray;
    }
}
