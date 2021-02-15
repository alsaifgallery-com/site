<?php


namespace AlsaifGallery\ForgotPassword\Api;

interface VerifyCodeManagementInterface
{

    /**
     * POST for verify Code api
     * @param string $code
     * @return string
     */
    public function postVerifyCode($code);
}
