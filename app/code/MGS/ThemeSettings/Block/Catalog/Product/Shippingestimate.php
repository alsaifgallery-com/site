<?php
namespace MGS\ThemeSettings\Block\Catalog\Product;

class Shippingestimate extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Country $country,
        array $data = []
    ) {
        $this->country = $country;
        parent::__construct($context, $data);
    }

    /**
     * Get the list of regions present in the given Country
     * Returns empty array if no regions available for Country
     *
     * @param String
     * @return Array/Void
    */
    public function getRegionsOfCountry($countryCode) {
        $regionCollection = $this->country->loadByCode($countryCode)->getRegions();
        $regions = $regionCollection->loadData()->toOptionArray(false);
        return $regions;
    }
  }
