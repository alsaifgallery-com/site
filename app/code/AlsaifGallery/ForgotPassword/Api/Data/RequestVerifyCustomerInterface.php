<?php


namespace AlsaifGallery\ForgotPassword\Api\Data;

interface RequestVerifyCustomerInterface 
{

    const TYPE = 'type';
    const IDENTITY = 'identity';
  

    /**
     * Get type
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     * @param string $type
     * @return \AlsaifGallery\ForgotPassword\Api\Data\RequestVerifyCustomerInterface
     */
    public function setType($type);
    /**
     * Get identity
     * @return string|null
     */
    public function getIdentity();

    /**
     * Set identity
     * @param string $identity
     * @return \AlsaifGallery\ForgotPassword\Api\Data\RequestVerifyCustomerInterface
     */
    public function setIdentity($identity);

   
}
