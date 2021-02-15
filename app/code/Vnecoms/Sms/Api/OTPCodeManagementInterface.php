<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vnecoms\Sms\Api;

interface OTPCodeManagementInterface
{

    /**
     * GET for OTPCode api
     * @param string $mobileNum
     * @return string[]
     */
    public function getOTPCode($mobileNum);
}
