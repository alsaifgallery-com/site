<?php


namespace AlsaifGallery\RestAPIConfig\Api;

interface GetCodeManagementInterface
{

    /**
     * GET for getCode api
     * @param string $mobile
     * @return string
     */
    public function getGetCode($mobile);
}
