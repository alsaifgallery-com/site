<?php


namespace AlsaifGallery\ForgotPassword\Api;

interface VerifyCustomerManagementInterface
{

    /**
     * POST for verifyCustomer api
     * @param string $identity accept email or phone
     * @return string[]
     */
    public function postVerifyCustomer($identity);

    /**
     * POST for SendCode api
     * @param string $identity accept email or phone
     * @param string $type [email or phone ]
     * @return string[]
     */
    public function postSendCode($identity,$type);
}
