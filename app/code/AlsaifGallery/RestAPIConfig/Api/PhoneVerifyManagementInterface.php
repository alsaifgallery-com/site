<?php


namespace AlsaifGallery\RestAPIConfig\Api;

interface PhoneVerifyManagementInterface
{

    /**
     * GET for getCode api
     * @param string $phoneCode
     * @param string $phone
     * @return string
     */
    public function getGetCode ( $phoneCode , $phone );
    
    /**
     * GET for verifyCode api
     * @param string $customerId
     * @param string $phoneCode
     * @param string $phone
     * @param string $code
     * @param string $addressId
     * @return string[]
     */
    public function getVerifyCode($customerId, $phoneCode , $phone , $code , $addressId=null);
    
}
