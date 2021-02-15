<?php

namespace AlsaifGallery\CountriesDirectory\Model;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use \Custom\City\Model\Resource\City\CollectionFactory;

class CountriesListManagement implements \AlsaifGallery\CountriesDirectory\Api\CountriesListManagementInterface
{

    public $cityCollection, $countryAcquirer, $countryFactory, $regionFactory, $directoryHelper, $storeManager, $scopeConfig;

    public function __construct(CollectionFactory $cityCollection,
        CountryInformationAcquirerInterface $countryAcquirer,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionFactory,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->cityCollection = $cityCollection;
        $this->countryAcquirer = $countryAcquirer;
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->directoryHelper = $directoryHelper;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountriesList($code)
    {
        $store = $this->storeManager->getStore();
//        return $store->getId();
        //        return \MongoDB\BSON\toJSON($store);
        //        $countriesCollection = $this->directoryHelper->getCountryCollection($store)->load();
        //         return json_encode($countriesCollection->getData());
        $base_url = $this->storeManager->getStore()->getBaseUrl();
        $urlMedia = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
//        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
//        $this->scopeConfig->getValue($path)
        $country = $this->countryFactory->create()->loadByCode($param);
        if ($country->getId()) {
            $arr = array(
                'country_id' => '',
                'country_name' => '',
                'country_tele_code' => '',
                'flag' => '',
                'cities' => array(),
            );

            $cityCollection = $this->cityCollection->create()->addFilter('country_id', $param);
//        return json_encode($country->getData());
            $arr['country_id'] = $country->getId();
            $arr['country_name'] = $country->getName('en_US');
            $arr['country_tele_code'] = $country->getData()['country_tele_code'];
            $arr['flag'] = $base_url . '/pub/media/flags/' . strtolower($param) . '.png';
            // $arr['flag'] = trim($urlMedia, "/") . '/flags/' . strtolower($param) . '.png';
            foreach ($cityCollection->getData() as $city) {
                $cityArr = array(
                    'city_id' => '',
                    'city_name' => '',
                );
                $cityArr['city_id'] = $city['id'];
                $cityArr['city_name'] = $city['city'];
                array_push($arr['cities'], $cityArr);
            }
//        $regions_collection= $this->regionFactory->create();
            //        $regions_collection->addCountryFilter($param);
            return json_encode($arr);
        } else {
            return 'Sorry this country code does not match any record';
        }
//        return json_encode($country->getData());
        //        return json_encode($this->countryAcquirer->getCountriesInfo());
        //        return json_encode($cityCollection->getData());
        //        returtn 'country code is ' . $param;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountries($storeCode)
    {
        $base_url = $this->scopeConfig->getValue("web/unsecure/base_url"); //$this->storeManager->getStore()->getBaseUrl();
        $urlMedia = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $store = $this->storeManager->getStore();
        $allowed_countries = $this->scopeConfig->getValue('general/country/allow', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        $allowed_countries = explode(',', $allowed_countries);
        $countries = array();
        foreach ($allowed_countries as $code) {
//         return $code;
            $country = $this->countryFactory->create()->loadByCode($code);
            if ($country->getId() && $country->getId() != "SA") {
                $arr = array(
                    'cities' => array(),
                );
                $cityCollection = $this->cityCollection->create()->addFilter('country_id', $code);
                if($storeCode == 'ar') {
                  $arr['country_name'] = $country->getName('ar_SA');
                }else {
                  $arr['country_name'] = $country->getName('en_US');
                }
                $arr['country_id'] = $country->getId();

                $arr['country_tele_code'] = $country->getData()['country_tele_code'];
                $arr['flag'] = $base_url . 'pub/media/flags/' . strtolower($code) . '.png';
                if (count($cityCollection->getData())) {
                    foreach ($cityCollection->getData() as $city) {
                        $cityArr = array(
                        );
                        $cityArr['city_id'] = $city['id'];
                        $cityArr['city_name'] = $city['city'];
                        array_push($arr['cities'], $cityArr);
                    }
                }
                array_push($countries, $arr);
            }

            if ($country->getId() && $country->getId() == "SA") {
              $SA_arr = array(
                  'cities' => array(),
              );
              $SA_cityCollection = $this->cityCollection->create()->addFilter('country_id', $code);
              if($storeCode == 'ar') {
                $SA_arr['country_name'] = $country->getName('ar_SA');
              }else {
                $SA_arr['country_name'] = $country->getName('en_US');
              }
              $SA_arr['country_id'] = $country->getId();

              $SA_arr['country_tele_code'] = $country->getData()['country_tele_code'];
              $SA_arr['flag'] = $base_url . 'pub/media/flags/' . strtolower($code) . '.png';
              if (count($SA_cityCollection->getData())) {
                  foreach ($SA_cityCollection->getData() as $SA_city) {
                      $SA_cityArr = array(
                      );
                      $SA_cityArr['city_id'] = $SA_city['id'];
                      $SA_cityArr['city_name'] = $SA_city['city'];
                      array_push($SA_arr['cities'], $SA_cityArr);
                  }
              }
              array_unshift($countries , $SA_arr);
            }


        }
        return $countries;
    }

}
