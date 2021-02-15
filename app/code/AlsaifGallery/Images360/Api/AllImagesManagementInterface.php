<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\Images360\Api;

interface AllImagesManagementInterface
{

    /**
     * GET for allImages api
     * @param string $id
     * @return array
     */
    public function getAllImages($id);
}
