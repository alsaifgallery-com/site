<?php


namespace AlsaifGallery\CountriesDirectory\Api;

interface CountriesListManagementInterface
{

    /**
     * GET for TeleCode api
     * @param string $param
     * @return string
     */

    public function getCountriesList($code);
    /**
     * Get allowed countries
     * @param string $param
     * @return string
     */
    public function getCountries($storeCode);
}
