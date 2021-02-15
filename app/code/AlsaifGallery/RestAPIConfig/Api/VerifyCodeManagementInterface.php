<?php

namespace AlsaifGallery\RestAPIConfig\Api;

interface VerifyCodeManagementInterface
{

    /**
     * GET for verifyCode api
     * @param string $code
     * @param string $value
     * @param string $verify_type
     * @param string $entity
     * @param string $entity_id
     * @return string
     */
    public function getVerifyCode($code, $value, $verify_type, $entity, $entity_id);
}
