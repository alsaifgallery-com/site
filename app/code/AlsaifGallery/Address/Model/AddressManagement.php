<?php
namespace AlsaifGallery\Address\Model;

class AddressManagement
      implements \AlsaifGallery\Address\Api\AddressManagementInterface {

    protected $addressHelper;
    protected $customerRepo;
    protected $addressRepo;
    protected $addressFactory;

    protected $customer;
    protected $customerFactory;
    protected $searchCriteriaBuilder;

    /**
     *
     * @param \AlsaifGallery\Address\Helper\Data $addressHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo
     */
    public function __construct(
        \AlsaifGallery\Address\Helper\Data $addressHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo,
        \Magento\Customer\Api\AddressRepositoryInterface  $addressRepo,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory,
        \Magento\Customer\Api\Data\RegionInterfaceFactory $regionFactory ,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
      $this->addressHelper = $addressHelper;
      $this->customerRepo = $customerRepo;
      $this->addressRepo = $addressRepo;
      $this->addressFactory = $addressFactory;
      $this->regionFactory = $regionFactory;

        $this->customerFactory = $customerFactory;
        $this->customer = $customers;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAddresses($customerId) {
        $customer = $this->customerRepo->getById($customerId);
        if( !is_null($customer) && $customer->getId()){
           $this->addressHelper->loadAddressDataNeeded($customer) ;
           return $customer->getAddresses();
        }
        return [];
    }
    /**
     * {@inheritdoc}
     */
    private function getAllAddressesAfterUpdate($customerId) {
        $customer = $this->customer->load($customerId);
        if( !is_null($customer) && $customer->getId()){
            $dataCustomerModel = $customer->getDataModel();
            $this->addressHelper->loadAddressDataNeeded($dataCustomerModel) ;
           return $dataCustomerModel->getAddresses();
        }
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public function postAddAddresses( $customerId ,$firstname=null,$lastname=null,
                                      $countryId, $city ,
                                      $street,$telephone,
                                      $regionCode,$region,
                                      $phoneCode ="966",
                                      $extraInfo ="",
                                      $default=false, $postcode="",
                                      $regionId=0,$addressLabel=null,
                                      $addressLatitude=null,$addressLongitude=null
    ) {
        // $isDefaultAddress = boolval($default);
        // 1 0 yes no true false
        $isDefaultAddress = filter_var( $default , FILTER_VALIDATE_BOOLEAN);
        $newAddress = $this->addressFactory->create();
        $customer = $this->customerRepo->getById($customerId);
           // build address
           if(!is_null($firstname) && !empty($firstname)){
            $newAddress->setFirstname( $firstname );
           }else{
            $newAddress->setFirstname( $customer->getFirstname());
           }

           if(!is_null($lastname) && !empty($lastname)){
               $newAddress->setLastname( $lastname );
           }else{
               $newAddress->setLastname( $customer->getLastname() );
           }

           $newAddress->setCustomerId($customerId);

           if(!is_null($countryId) && !empty($countryId)){
                $newAddress->setCountryId($countryId);
           }
           if(!is_null($city) && !empty($city)){
                $newAddress->setCity($city);
           }
           if(!is_null($street) && !empty($street)){
                $newAddress->setStreet([$street]);
           }
           if(!is_null($postcode) ){
                $newAddress->setPostcode($postcode);
           }

           if(!is_null($regionId) && $regionId > 0 ){
                $newAddress->setRegionId($regionId);
           }else{

              if( $this->isRegionCodeExists($regionCode , $countryId)){
                  $regionIdKey = $this->getRegionIdByCode($regionCode , $countryId);
                  $newAddress->setRegionId($regionIdKey);
              }else{
                $regionId = 0;
                $regionobj = $this->regionFactory->create();
                // \Magento\Customer\Api\Data\RegionInterface::class;
                $regionobj->setRegion($region);
                $regionobj->setRegionCode($regionCode);
                $regionobj->setRegionId($regionId);

                $newAddress->setRegionId($regionId);
                $newAddress->setRegion($regionobj);
              }

           }

           if(!is_null($telephone) && !empty($telephone)){
                $newAddress->setTelephone($telephone);
           }else{
                $newAddress->setTelephone("");
           }
           if(!is_null($addressLabel) && !empty($addressLabel)){
                $newAddress->setCustomAttribute("address_label", $addressLabel);
           }
           if(!is_null($addressLatitude) && !empty($addressLatitude)){
                $newAddress->setCustomAttribute("address_latitude", $addressLatitude);
           }
           if(!is_null($addressLongitude) && !empty($addressLongitude)){
                $newAddress->setCustomAttribute("address_longitude", $addressLongitude);
           }
           if(!is_null($phoneCode) && !empty($phoneCode)){
                $newAddress->setCustomAttribute("phone_code", $phoneCode);
           }
           if(!is_null($extraInfo) && !empty($extraInfo)){
                $newAddress->setCustomAttribute("extra_info", $extraInfo);
           }

            $newAddress->setIsDefaultBilling($isDefaultAddress);
            $newAddress->setIsDefaultShipping($isDefaultAddress);


           $addedAddress = $this->addressRepo->save($newAddress);


        return $this->getAllAddressesAfterUpdate($customerId);
    }
    /**
     * {@inheritdoc}
     */
    public function postUpdateAddAddresses($customerId,$addressId,$firstname=null,$lastname=null,
                                      $countryId=null, $city=null,
                                      $street=null,$telephone=null,
                                      $regionCode=null,$region=null,
                                      $phoneCode =null,$extraInfo=null,$default=null,
                                      $postcode=null,
                                      $regionId=null,$addressLabel=null,
                                      $addressLatitude=null,$addressLongitude=null
    ) {
        $countryIdNow = null;
        $address = $this->addressRepo->getById($addressId);

        if( is_null($address ) ||  !$address->getId() ){
          throw new \Magento\Framework\Exception\LocalizedException(
                __("Can not find this address.")
            );
        }

        if( $address->getCustomerId() != $customerId ){
            throw new \Magento\Framework\Exception\LocalizedException(
                __("This Address not found in currunt Customer Addresses.")
            );
        }
        $countryIdNow = $address->getCountryId();

        if(!is_null($firstname) && !empty($firstname)){
            $address->setFirstname( $firstname);
        }

        if(!is_null($lastname) && !empty($lastname)){
            $address->setLastname( $lastname );
        }

        if(!is_null($countryId) && !empty($countryId)){
             $address->setCountryId($countryId);
             $countryIdNow = $countryId;
        }
        if(!is_null($city) && !empty($city)){
             $address->setCity($city);
        }
        if(!is_null($street) && !empty($street)){
             $address->setStreet([$street]);
        }
        if(!is_null($postcode) && !empty($postcode) ){
             $address->setPostcode($postcode);
        }
//        if(!is_null($regionId)  && !empty($regionId) ){
//             $address->setRegionId($regionId);
//        }


        if(!is_null($regionId) && $regionId > 0 ){
             $address->setRegionId($regionId);
        }else{
            if( $this->isRegionCodeExists($regionCode , $countryIdNow)){
                $regionIdKey = $this->getRegionIdByCode($regionCode , $countryIdNow);
                $address->setRegionId($regionIdKey);
            }else{
                $regionId = 0;
                $regionobj = $address->getRegion();
                if (is_null($regionobj)) {
                    $regionobj = $this->regionFactory->create();
                }
                // \Magento\Customer\Api\Data\RegionInterface::class;
                if (!is_null($region) && !empty($region)) {
                    $regionobj->setRegion($region);
                }
                if (!is_null($regionCode) && !empty($regionCode)) {
                    $regionobj->setRegionCode($regionCode);
                }
                $regionobj->setRegionId($regionId);

                $address->setRegionId($regionId);
                $address->setRegion($regionobj);
            }
        }

        if(!is_null($telephone) && !empty($telephone)){
             $address->setTelephone($telephone);
        }

        if(!is_null($addressLabel) && !empty($addressLabel)){
             $address->setCustomAttribute("address_label", $addressLabel);
        }
        if(!is_null($addressLatitude) && !empty($addressLatitude)){
             $address->setCustomAttribute("address_latitude", $addressLatitude);
        }
        if(!is_null($addressLongitude) && !empty($addressLongitude)){
             $address->setCustomAttribute("address_longitude", $addressLongitude);
        }
        if(!is_null($phoneCode) && !empty($phoneCode)){
            $address->setCustomAttribute("phone_code", $phoneCode);
        }
        if(!is_null($extraInfo) && !empty($extraInfo)){
            $address->setCustomAttribute("extra_info", $extraInfo);
        }

        if(!is_null($default)){
            // $isDefaultAddress = boolval($default);
            // 1 0 yes no true false
            $isDefaultAddress = filter_var( $default , FILTER_VALIDATE_BOOLEAN);
            $address->setIsDefaultBilling($isDefaultAddress);
            $address->setIsDefaultShipping($isDefaultAddress);
        }

        $updatedAddress = $this->addressRepo->save($address);

        return $this->getAllAddressesAfterUpdate($customerId);
    }
    /**
     * {@inheritdoc}
     */
    public function deleteAddresses($customerId, $addressId) {
        $address = $this->addressRepo->getById($addressId);
        if( !is_null($address ) && $address->getId() ){
            if( $address->getCustomerId() != $customerId ){
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("This Address not found in currunt Customer Addresses.")
                );
            }
            $status = $this->addressRepo->deleteById( $addressId );
            if($status == false ){
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The delete operation failed.")
                );
            }
        }

       return $this->getAllAddressesAfterUpdate($customerId);
    }

    private function getRegionIdByCode($regionCode , $countryId) {
        $regionIdData = $this->addressHelper->getRegionByCode($regionCode , $countryId);
        return $regionIdData->getRegionId();
    }

    private function isRegionCodeExists($regionCode  , $countryId ) {
        $ret = false;
        $regionId = $this->getRegionIdByCode($regionCode , $countryId);
        if ( $regionId > 0){
            $ret = true;
        }
        return $ret;
    }

}
