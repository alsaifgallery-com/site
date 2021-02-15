<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\TrackingInfo\Api;

interface TrackingInfoManagementInterface
{

    /**
     * GET for trackingInfo api
     * @param string $id
     * @return array
     */
    public function getTrackingInfo($id);
}
