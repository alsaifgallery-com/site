<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\ForceUpdate\Api;

interface ForceUpdateManagementInterface
{

    /**
     * GET for ForceUpdate api
     * @return boolean
     */
    public function getForceUpdate();
}
