<?php

namespace AlsaifGallery\Address\Helper;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Directory\Model\RegionFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $addressExtensionFactory;
    protected $country;
    protected $customerRepo ;
    protected $addressRepo ;
    protected $customerRegistry;
    protected $addressRegistry;

    protected $regionDataFactory;
    protected $regionFactory;

    public function __construct(
            \Magento\Framework\App\Helper\Context $context,
            \Magento\Customer\Api\Data\AddressExtensionInterfaceFactory $addressExtensionFactory,
            \Magento\Directory\Model\Country $country,

            \Magento\Customer\Model\AddressRegistry $addressRegistry,
            \Magento\Customer\Model\CustomerRegistry $customerRegistry,

            \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo,
            \Magento\Customer\Api\AddressRepositoryInterface  $addressRepo,

            \Magento\Customer\Api\Data\RegionInterfaceFactory $regionDataFactory,
            \Magento\Directory\Model\RegionFactory $regionFactory
    ) {
        $this->addressExtensionFactory = $addressExtensionFactory;
        $this->country = $country;
        $this->customerRepo = $customerRepo;
        $this-> addressRepo = $addressRepo;
        $this->addressRegistry = $addressRegistry;
        $this->customerRegistry = $customerRegistry;

        $this->regionDataFactory = $regionDataFactory;
        $this->regionFactory = $regionFactory;

        parent::__construct($context);
    }

    public function getAddressTeleCode($code) {
        $country = $this->country->load($code);
        $country_tele_code = $country->getData('country_tele_code');
        return $country_tele_code;
    }

    public function loadAddressDataNeeded(\Magento\Customer\Api\Data\CustomerInterface $customer) {
        $addresses = $customer->getAddresses();
        foreach ($addresses as $address) {
//           return $address->getCountryId();
            $extn = $address->getExtensionAttributes();
            if (is_null($extn)) {
                $extn = $this->addressExtensionFactory->create();
            }

            $extn->setAddressLabel("");
            $addressLabelAttr = $address->getCustomAttribute("address_label");
            if (!is_null($addressLabelAttr)) {
                $extn->setAddressLabel($addressLabelAttr->getValue());
            }

            $extn->setPhoneVerified('0');
            $addressPhoneVerifiedAttr = $address->getCustomAttribute("phone_verified");
            if (!is_null($addressPhoneVerifiedAttr)) {
                $extn->setPhoneVerified($addressPhoneVerifiedAttr->getValue());
            }

            $extn->setAddressLatitude("");
            $addressLatitudeAttr = $address->getCustomAttribute("address_latitude");
            if (!is_null($addressLatitudeAttr)) {
                $extn->setAddressLatitude($addressLatitudeAttr->getValue());
            }

            $extn->setAddressLongitude("");
            $addressLongitudeAttr = $address->getCustomAttribute("address_longitude");
            if (!is_null($addressLongitudeAttr)) {
                $extn->setAddressLongitude($addressLongitudeAttr->getValue());
            }

            $extn->setAddressTeleCode($this->getAddressTeleCode($address->getCountryId()));


            $extn->setPhoneCode("");
            $addressPhoneCodeeAttr = $address->getCustomAttribute("phone_code");
            if (!is_null($addressPhoneCodeeAttr)) {
                $extn->setPhoneCode($addressPhoneCodeeAttr->getValue());
            }
            $extn->setExtraInfo('');
            $extraInfoAttr = $address->getCustomAttribute("extra_info");
            if (!is_null($extraInfoAttr)) {
                $extn->setExtraInfo($extraInfoAttr->getValue());
            }

            $extn->setIsDefaultBilling( $address->isDefaultBilling());
            $extn->setIsDefaultShipping( $address->isDefaultShipping());


            $address->setExtensionAttributes($extn);
        }
        $customer->setAddresses($addresses);
    }


    public function getRegionByCode($regionCode , $countryId) {
        $region     = $this->regionFactory->create();
        $regionData = $this->regionDataFactory->create();

        $region->loadByCode( $regionCode , $countryId);

        if (! $region->getId()) {
            $region->loadByName( $regionCode , $countryId);
        }

        if ($region->getId()) {
            $regionData
                ->setRegionId($region->getId())
                ->setRegionCode($region->getCode())
                ->setRegion($region->getDefaultName());
        } else {
            $regionData->setRegion( $regionCode );
        }

        return $regionData;
    }

    /**
     * Convert Magento address to array for json encode
     *
     * @param AddressInterface $address
     *
     * @return array
     */
    public function convertToArray(AddressInterface $address)
    {
        // based on : vendor/amzn/amazon-pay-module/Helper/Address.php
        $data = [
            AddressInterface::CITY       => $address->getCity(),
            AddressInterface::FIRSTNAME  => $address->getFirstname(),
            AddressInterface::LASTNAME   => $address->getLastname(),
            AddressInterface::COUNTRY_ID => $address->getCountryId(),
            AddressInterface::STREET     => $address->getStreet(),
            AddressInterface::POSTCODE   => $address->getPostcode(),
            AddressInterface::COMPANY    => $address->getCompany(),
            AddressInterface::TELEPHONE  => null,
            AddressInterface::REGION     => null,
            AddressInterface::REGION_ID  => null,
            'region_code'                => null
        ];

        if ($address->getTelephone()) {
            $data[AddressInterface::TELEPHONE] = $address->getTelephone();
        }

        if ($address->getRegion()) {
            $data[AddressInterface::REGION] = $address->getRegion()->getRegion();

            if ($address->getRegion()->getRegionId()) {
                $data[AddressInterface::REGION_ID] = $address->getRegion()->getRegionId();
                $data['region_code']               = $address->getRegion()->getRegionCode();
            }
        }

        return $data;
    }


}
