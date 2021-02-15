<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\SocialLogin\Api;

interface SocialTokenManagementInterface
{

    /**
     * GET for SocialToken api
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSocialToken(\Magento\Customer\Api\Data\CustomerInterface $customer);
}
