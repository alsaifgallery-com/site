<?php

namespace AlsaifGallery\Address\Api;

interface AddressManagementInterface {

    /**
     * 
     * @param string $customerId
     * @return \Magento\Customer\Api\Data\AddressInterface[]|null
     */
    public function getAllAddresses($customerId);

    /**
     * 
     * @param string $customerId
     * @param string $firstname
     * @param string $lastname
     * @param string $countryId SA,EG,...
     * @param string $city
     * @param string $street
     * @param string $telephone
     * @param string $regionCode
     * @param string $region
     * @param string $phoneCode  +966,...
     * @param string $extraInfo  
     * @param string $default    is default shipping and billing address
     * @param string $postcode
     * @param string $regionId
     * @param string $addressLabel
     * @param string $addressLatitude
     * @param string $addressLongitude
     * @return \Magento\Customer\Api\Data\AddressInterface[]|null
     */
    public function postAddAddresses($customerId, $firstname = null, $lastname = null,
            $countryId, $city,
            $street, $telephone,
            $regionCode, $region,
            $phoneCode = "966",
            $extraInfo = "",
            $default = false, $postcode = "",
            $regionId = 0, $addressLabel = null,
            $addressLatitude = null, $addressLongitude = null
    );

    /**
     * 
     * @param string $customerId
     * @param string $addressId
     * @param string $firstname
     * @param string $lastname
     * @param string $countryId SA,EG,...
     * @param string $city
     * @param string $street
     * @param string $telephone
     * @param string $regionCode
     * @param string $region
     * @param string $phoneCode
     * @param string $extraInfo
     * @param string $default
     * @param string $postcode
     * @param string $regionId
     * @param string $addressLabel
     * @param string $addressLatitude
     * @param string $addressLongitude
     * @return \Magento\Customer\Api\Data\AddressInterface[]|null
     */
    public function postUpdateAddAddresses($customerId, $addressId, $firstname = null, $lastname = null,
            $countryId = null, $city = null,
            $street = null, $telephone = null,
            $regionCode = null, $region = null,
            $phoneCode = null, $extraInfo=null, $default = null,
            $postcode = null,
            $regionId = null, $addressLabel = null,
            $addressLatitude = null, $addressLongitude = null
    );

    /**
     * 
     * @param string $customerId
     * @param string $addressId
     * @return \Magento\Customer\Api\Data\AddressInterface[]|null
     */
    public function deleteAddresses($customerId, $addressId);
}
