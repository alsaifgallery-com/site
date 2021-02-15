<?php


namespace AlsaifGallery\ForgotPassword\Api;

interface ResetPasswordManagementInterface
{

    /**
     * POST for resetPassword api
     * @param string $token
     * @param string $password
     * @return string
     */
    public function postResetPassword($token,$password);
}
