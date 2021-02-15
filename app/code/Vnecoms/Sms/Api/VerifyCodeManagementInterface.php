<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vnecoms\Sms\Api;

interface VerifyCodeManagementInterface
{

    /**
     * GET for VerifyCode api
     * @param string $mobileNum
     * @param string $code
     * @return string[]
     */
    public function getVerifyCode($mobileNum, $code);
}
